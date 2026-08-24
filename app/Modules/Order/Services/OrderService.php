<?php

declare(strict_types=1);

namespace App\Modules\Order\Services;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Repositories\OrderRepository;
use App\Modules\Cart\Services\CartService;
use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Models\CustomerAddress;
use App\Modules\Promotion\Services\CouponService;
use App\Modules\SalesInvoice\Services\SalesInvoiceService;
use App\Modules\Shipping\Services\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use App\Modules\Cart\Models\Cart;

final class OrderService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
        private readonly SalesInvoiceService $invoiceService,
        private readonly OrderRepository $repository,
        private readonly ShippingService $shippingService
    ) {
    }
    public function checkout(array $data, ?Customer $customer): Order
    {
        return DB::transaction(function () use ($data, $customer) {
            $guestToken = $customer ? null : $data["guest_token"] ?? null;
            $cart = $this->cartService
                ->get($customer?->id, $guestToken)
                ->load("items");
            if ($cart->items->isEmpty()) {
                throw new RuntimeException("Cart is empty.");
            }
            $billing = $this->resolveAddress(
                $customer,
                $data["billing_address_uuid"] ?? null,
                $data["billing_address"] ?? null
            );
            $shipping = $this->resolveAddress(
                $customer,
                $data["shipping_address_uuid"] ?? null,
                $data["shipping_address"] ?? null
            );
            if (!$shipping) {
                throw new RuntimeException("Shipping address is required.");
            }

            /*
             * Destination-aware tax:
             * Recompute every line tax against the selected shipping
             * destination (country/state) before totals are frozen.
             */
            $destination = $this->normalizeDestination($shipping);

            $cart = $this->cartService->recalculate(
                $cart->load("items"),
                $destination
            );

            /*
             * Shipping is validated and priced server-side BEFORE payment.
             * Either the rate explicitly chosen at checkout, or the one
             * already applied to the cart during the shipping step, is
             * re-verified against the destination and live cart amounts.
             */
            $cart = $this->applyCheckoutShipping(
                $cart,
                $data["shipping_rate_uuid"] ?? null,
                $destination
            );

            if ($cart->coupon_code) {
                $coupon = $this->couponService->validate(
                    $cart->coupon_code,
                    (float) $cart->subtotal,
                    $customer?->id
                );
                $discount = $this->couponService->discount(
                    $coupon,
                    (float) $cart->subtotal
                );
                $cart->update([
                    "discount_amount" => $discount,
                    "grand_total" => max(
                        0,
                        (float) $cart->subtotal -
                            $discount +
                            (float) $cart->tax_amount +
                            (float) $cart->shipping_amount
                    ),
                ]);
                $cart->refresh();
            }
            $this->reserve($cart);
            $order = Order::create([
                "customer_id" => $customer?->id,
                "guest_token" => $guestToken,
                "customer_email" => $data["email"],
                "customer_name" => trim(
                    ($data["first_name"] ?? "") .
                        " " .
                        ($data["last_name"] ?? "")
                ),
                "customer_phone" => $data["phone"] ?? null,
                "status" => Order::PENDING,
                "payment_status" =>
                    $data["payment_method"] === "cod" ? "pending" : "pending",
                "fulfillment_status" => "unfulfilled",
                "currency_code" => $cart->currency_code,
                "subtotal" => $cart->subtotal,
                "discount_amount" => $cart->discount_amount,
                "tax_amount" => $cart->tax_amount,
                "shipping_amount" => $cart->shipping_amount,
                "shipping_rate_uuid" => $cart->shipping_rate_uuid,
                "shipping_method_uuid" => $cart->shipping_method_uuid,
                "shipping_method_name" => $cart->shipping_method_name,
                "shipping_method_code" => $cart->shipping_method_code,
                "grand_total" => $cart->grand_total,
                "coupon_code" => $cart->coupon_code,
                "payment_method" => $data["payment_method"],
                "billing_address" => $billing,
                "shipping_address" => $shipping,
                "customer_note" => $data["customer_note"] ?? null,
                "placed_at" => now(),
            ]);
            foreach ($cart->items as $i) {
                $order
                    ->items()
                    ->create(
                        $i->only([
                            "product_id",
                            "product_variant_id",
                            "sku",
                            "product_name",
                            "variant_name",
                            "quantity",
                            "unit_price",
                            "discount_amount",
                            "tax_rate",
                            "tax_amount",
                            "line_subtotal",
                            "line_total",
                        ])
                    );
            }
            $order
                ->histories()
                ->create([
                    "from_status" => null,
                    "to_status" => Order::PENDING,
                    "source" => "checkout",
                    "note" => "Order created",
                ]);
            if ($cart->coupon_code) {
                $coupon = $this->couponService->validate(
                    $cart->coupon_code,
                    (float) $cart->subtotal,
                    $customer?->id
                );
                $this->couponService->redeem(
                    $coupon,
                    $customer?->id,
                    $order->id,
                    (float) $cart->discount_amount
                );
            }
            $this->invoiceService->createForOrder($order->load("items"));
            $cart->update(["status" => Cart::CONVERTED]);
            return $order->load("items", "histories");
        });
    }
    private function resolveAddress(
        ?Customer $customer,
        ?string $uuid,
        ?array $input
    ): ?array {
        if ($uuid && $customer) {
            $a = CustomerAddress::where("customer_id", $customer->id)
                ->where("uuid", $uuid)
                ->where("is_active", true)
                ->firstOrFail();
            return $a->toArray();
        }
        return $input;
    }

    /**
     * Normalize any address payload into canonical destination keys.
     *
     * Handles both saved addresses (country_code/state_code) and inline
     * payloads (country/state) coming from guest checkout.
     *
     * @param array<string, mixed>|null $address
     * @return array<string, string>
     */
    private function normalizeDestination(?array $address): array
    {
        if (!$address) {
            return [];
        }

        return [
            "country_code" => strtoupper(
                trim((string) ($address["country_code"] ?? $address["country"] ?? ""))
            ),
            "state_code" => strtoupper(
                trim((string) ($address["state_code"] ?? $address["state"] ?? ""))
            ),
            "postal_code" => trim((string) ($address["postal_code"] ?? "")),
        ];
    }

    /**
     * Validate and apply the shipping selection before payment.
     *
     * Priority: explicit rate from the checkout request, otherwise the
     * rate already applied to the cart during the shipping step. The
     * amount is ALWAYS recalculated server-side; a rate that no longer
     * applies to the destination/amount aborts the checkout.
     *
     * When no shipping selection exists at all the cart keeps its
     * current (zero) shipping amount so digital/free-shipping flows
     * keep working.
     *
     * @param Cart $cart Recalculated cart.
     * @param string|null $rateUuid Rate chosen on the checkout request.
     * @param array<string, string> $destination Normalized destination.
     * @return Cart Cart with final totals.
     *
     * @throws \RuntimeException When the selected rate is not applicable.
     */
    private function applyCheckoutShipping(
        Cart $cart,
        ?string $rateUuid = null,
        array $destination = []
    ): Cart {
        $rateUuid = $rateUuid ?: ($cart->shipping_rate_uuid ?: null);

        if (!$rateUuid) {
            return $cart->load("items");
        }

        $selected = $this->shippingService->resolveSelectedRate($rateUuid, [
            "country_code" => $destination["country_code"] ?? "",
            "state_code" => $destination["state_code"] ?? "",
            "postal_code" => $destination["postal_code"] ?? "",
            "order_amount" => (float) $cart->subtotal,
            "weight" => 0.0,
        ]);

        return $this->cartService->setShipping($cart, [
            "shipping_rate_uuid" => $selected["uuid"],
            "shipping_method_uuid" => $selected["method"]["uuid"],
            "shipping_method_name" => $selected["method"]["name"],
            "shipping_method_code" => $selected["method"]["code"],
            "shipping_amount" => $selected["amount"],
            "estimated_delivery_min_days" => $selected["estimated_days"]["min"] ?? null,
            "estimated_delivery_max_days" => $selected["estimated_days"]["max"] ?? null,
            "shipping_address" => $destination ?: null,
        ]);
    }
    private function reserve($cart): void
    {
        foreach ($cart->items as $item) {
            $q = DB::table("inventory_stocks")->where(
                "product_id",
                $item->product_id
            );
            if ($item->product_variant_id) {
                $q->where("product_variant_id", $item->product_variant_id);
            }
            $stock = $q->lockForUpdate()->first();
            if (!$stock) {
                throw new RuntimeException(
                    "Stock record not found for " . $item->sku
                );
            }
            $available =
                (float) $stock->quantity - (float) $stock->reserved_quantity;
            if ($available < (float) $item->quantity) {
                throw new RuntimeException(
                    "Insufficient stock for " . $item->sku
                );
            }
            DB::table("inventory_stocks")
                ->where("id", $stock->id)
                ->update([
                    "reserved_quantity" =>
                        (float) $stock->reserved_quantity +
                        (float) $item->quantity,
                    "updated_at" => now(),
                ]);
            if (Schema::hasTable("inventory_transactions")) {
                DB::table("inventory_transactions")->insert([
                    "uuid" => (string) Str::uuid(),
                    "inventory_stock_id" => $stock->id,
                    "product_id" => $item->product_id,
                    "product_variant_id" => $item->product_variant_id,
                    "type" => "sales_reserve",
                    "quantity" => $item->quantity,
                    "quantity_before" => $stock->quantity,
                    "quantity_after" => $stock->quantity,
                    "reference_type" => "cart",
                    "reference_id" => $cart->uuid,
                    "notes" => "Stock reserved for checkout",
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);
            }
        }
    }
    public function listForCustomer(int $customerId, array $filters)
    {
        return $this->repository->paginateForCustomer($customerId, $filters);
    }
    public function showForCustomer(int $customerId, string $uuid): Order
    {
        return $this->repository->findByCustomerUuidOrFail($customerId, $uuid);
    }
    public function guestShow(string $orderNumber, ?string $guestToken): Order
    {
        if (!$guestToken) {
            throw new RuntimeException("Guest token is required.");
        }
        return Order::with("items", "histories")
            ->where("order_number", $orderNumber)
            ->where("guest_token", $guestToken)
            ->firstOrFail();
    }
    public function cancel(Order $order, ?string $reason): Order
    {
        if (
            !in_array($order->status, [Order::PENDING, Order::CONFIRMED], true)
        ) {
            throw new RuntimeException(
                "Order cannot be cancelled at this stage."
            );
        }
        $from = $order->status;
        DB::transaction(function () use ($order, $reason, $from) {
            $this->releaseReservation($order);
            $order->update([
                "status" => Order::CANCELLED,
                "cancelled_at" => now(),
                "cancellation_reason" => $reason,
            ]);
            $order
                ->histories()
                ->create([
                    "from_status" => $from,
                    "to_status" => Order::CANCELLED,
                    "source" => "customer",
                    "note" => $reason,
                ]);
        });
        return $order->fresh("items", "histories");
    }
    public function releaseReservation(Order $order): void
    {
        foreach ($order->items as $item) {
            $q = DB::table("inventory_stocks")->where(
                "product_id",
                $item->product_id
            );
            if ($item->product_variant_id) {
                $q->where("product_variant_id", $item->product_variant_id);
            }
            $stock = $q->lockForUpdate()->first();
            if (!$stock) {
                continue;
            }
            $new = max(
                0,
                (float) $stock->reserved_quantity - (float) $item->quantity
            );
            DB::table("inventory_stocks")
                ->where("id", $stock->id)
                ->update(["reserved_quantity" => $new, "updated_at" => now()]);
            if (Schema::hasTable("inventory_transactions")) {
                DB::table("inventory_transactions")->insert([
                    "uuid" => (string) Str::uuid(),
                    "inventory_stock_id" => $stock->id,
                    "product_id" => $item->product_id,
                    "product_variant_id" => $item->product_variant_id,
                    "type" => "sales_release",
                    "quantity" => $item->quantity,
                    "quantity_before" => $stock->quantity,
                    "quantity_after" => $stock->quantity,
                    "reference_type" => "order",
                    "reference_id" => $order->uuid,
                    "notes" => "Stock reservation released",
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);
            }
        }
    }
    public function reorder(Order $order, int $customerId)
    {
        $cart = $this->cartService->get($customerId, null);
        foreach ($order->items as $item) {
            $productUuid = \App\Modules\Product\Models\Product::whereKey(
                $item->product_id
            )->value("uuid");
            $variantUuid = $item->product_variant_id
                ? \App\Modules\ProductVariant\Models\ProductVariant::whereKey(
                    $item->product_variant_id
                )->value("uuid")
                : null;
            if ($productUuid) {
                $this->cartService->add(
                    $cart,
                    $productUuid,
                    $variantUuid,
                    (float) $item->quantity
                );
            }
        }
        return $this->cartService->get($customerId, null);
    }
    public function adminList(array $f)
    {
        return $this->repository->paginate($f);
    }
    public function adminShow(string $uuid): Order
    {
        return $this->repository->findByUuidOrFail($uuid);
    }

    public function adminSummary(string $uuid): array
    {
        $order = $this->adminShow($uuid);

        // Invoice
        $invoice = \App\Modules\SalesInvoice\Models\SalesInvoice::where('order_id', $order->id)->with('items')->first();

        // Payments (transactions)
        $payments = \App\Modules\Payment\Models\PaymentTransaction::where('order_id', $order->id)->latest()->get();

        // Shipments
        $shipments = \App\Modules\Shipment\Models\Shipment::with('items')->where('order_id', $order->id)->get();

        return [
            'order' => new \App\Modules\Order\Resources\OrderResource($order),
            'invoice' => $invoice ? new \App\Modules\SalesInvoice\Resources\SalesInvoiceResource($invoice) : null,
            'payments' => \App\Modules\Payment\Resources\PaymentTransactionResource::collection($payments),
            'shipments' => \App\Modules\Shipment\Resources\ShipmentResource::collection($shipments),
        ];
    }

    public function customerSummary(int $customerId, string $uuid): array
    {
        $order = Order::with('items', 'histories')
            ->where('uuid', $uuid)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $invoice = \App\Modules\SalesInvoice\Models\SalesInvoice::where('order_id', $order->id)->with('items')->first();
        $payments = \App\Modules\Payment\Models\PaymentTransaction::where('order_id', $order->id)->latest()->get();
        $shipments = \App\Modules\Shipment\Models\Shipment::with('items')->where('order_id', $order->id)->get();

        return [
            'order' => new \App\Modules\Order\Resources\OrderResource($order),
            'invoice' => $invoice ? new \App\Modules\SalesInvoice\Resources\SalesInvoiceResource($invoice) : null,
            'payments' => \App\Modules\Payment\Resources\PaymentTransactionResource::collection($payments),
            'shipments' => \App\Modules\Shipment\Resources\ShipmentResource::collection($shipments),
        ];
    }

    public function guestSummary(string $orderNumber, ?string $guestToken): array
    {
        $order = Order::with('items', 'histories')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->guest_token && $order->guest_token !== $guestToken) {
            throw new RuntimeException('Unauthorized access to guest order.');
        }

        $invoice = \App\Modules\SalesInvoice\Models\SalesInvoice::where('order_id', $order->id)->with('items')->first();
        $payments = \App\Modules\Payment\Models\PaymentTransaction::where('order_id', $order->id)->latest()->get();
        $shipments = \App\Modules\Shipment\Models\Shipment::with('items')->where('order_id', $order->id)->get();

        return [
            'order' => new \App\Modules\Order\Resources\OrderResource($order),
            'invoice' => $invoice ? new \App\Modules\SalesInvoice\Resources\SalesInvoiceResource($invoice) : null,
            'payments' => \App\Modules\Payment\Resources\PaymentTransactionResource::collection($payments),
            'shipments' => \App\Modules\Shipment\Resources\ShipmentResource::collection($shipments),
        ];
    }
    public function changeStatus(
        Order $order,
        string $status,
        ?string $note,
        int $userId
    ): Order {
        $from = $order->status;
        $order->update(["status" => $status]);
        $order
            ->histories()
            ->create([
                "from_status" => $from,
                "to_status" => $status,
                "changed_by" => $userId,
                "source" => "admin",
                "note" => $note,
            ]);
        return $order->fresh("items", "histories");
    }
}
