<?php

declare(strict_types=1);

namespace App\Modules\Cart\Services;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use RuntimeException;
final class CartService
{
    public function __construct(
        private readonly \App\Modules\Cart\Repositories\CartRepository $repository
    ) {
    }

    /**
     * Retrieve a paginated list of carts for admin purposes.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAdmin(array $filters)
    {
        return $this->repository->paginate($filters);
    }

    public function get(?int $customerId, ?string $guestToken): Cart
    {
        $q = Cart::with("items");
        if ($customerId) {
            $q->where("customer_id", $customerId)->where("status", Cart::ACTIVE);
        } elseif ($guestToken) {
            $q->where("guest_token", $guestToken)->where("status", Cart::ACTIVE);
        } else {
            $cart = Cart::create([
                "guest_token" => (string) Str::uuid(),
                "status" => Cart::ACTIVE,
            ]);
            return $cart->load("items");
        }
        return $q->first() ??
            Cart::create([
                "customer_id" => $customerId,
                "guest_token" => $customerId ? null : $guestToken,
                "status" => Cart::ACTIVE,
            ]);
    }
    public function add(
        Cart $cart,
        string $productUuid,
        ?string $variantUuid,
        float $qty
    ): Cart {
        return DB::transaction(function () use (
            $cart,
            $productUuid,
            $variantUuid,
            $qty
        ) {
            $product = Product::where("uuid", $productUuid)
                ->where("is_active", true)
                ->firstOrFail();
            $variant = $variantUuid
                ? ProductVariant::where("uuid", $variantUuid)
                    ->where("product_id", $product->id)
                    ->where("is_active", true)
                    ->firstOrFail()
                : null;
            $price = (float) ($variant?->price ?? $product->price);
            if ($price < 0) {
                throw new RuntimeException("Invalid product price.");
            }
            $item = $cart
                ->items()
                ->where("product_id", $product->id)
                ->where("product_variant_id", $variant?->id)
                ->first();
            if ($item) {
                $item->update(["quantity" => (float) $item->quantity + $qty]);
            } else {
                $cart
                    ->items()
                    ->create([
                        "product_id" => $product->id,
                        "tax_class_id" => $product->tax_class_id ?? null,
                        "product_variant_id" => $variant?->id,
                        "sku" => $variant?->sku ?? $product->sku,
                        "product_name" => $product->name,
                        "variant_name" => $variant?->name,
                        "quantity" => $qty,
                        "unit_price" => $price,
                        "compare_price" =>
                            (float) ($variant?->compare_price ??
                                $product->compare_price),
                        "line_subtotal" => $price * $qty,
                        "line_total" => $price * $qty,
                    ]);
            }
            return $this->recalculate($cart->fresh("items"));
        });
    }
    public function update(Cart $cart, CartItem $item, float $qty): Cart
    {
        $item->update(["quantity" => $qty]);
        return $this->recalculate($cart->fresh("items"));
    }
    public function remove(Cart $cart, CartItem $item): Cart
    {
        $item->delete();
        return $this->recalculate($cart->fresh("items"));
    }
    public function recalculate(Cart $cart): Cart
    {
        $subtotal = 0;
        $tax = 0;
        foreach ($cart->items as $item) {
            $line = (float) $item->unit_price * (float) $item->quantity;
            $rate = 0;
            if ($item->tax_class_id) {
                $rate =
                    (float) (\Illuminate\Support\Facades\DB::table("tax_rates")
                        ->where("tax_class_id", $item->tax_class_id)
                        ->where("is_active", true)
                        ->orderBy("priority")
                        ->value("rate") ?? 0);
            }
            $lineTax = round(($line * $rate) / 100, 2);
            $item->update([
                "tax_rate" => $rate,
                "tax_amount" => $lineTax,
                "line_subtotal" => $line,
                "line_total" => $line + $lineTax,
            ]);
            $subtotal += $line;
            $tax += $lineTax;
        }
        $discount = (float) $cart->discount_amount;
        $grand = max(
            0,
            $subtotal - $discount + $tax + (float) $cart->shipping_amount
        );
        $cart->update([
            "subtotal" => $subtotal,
            "tax_amount" => $tax,
            "grand_total" => $grand,
        ]);
        return $cart->fresh("items");
    }
    public function merge(Cart $customerCart, Cart $guestCart): Cart
    {
        return DB::transaction(function () use ($customerCart, $guestCart) {
            // Lock both carts to prevent concurrent modifications
            $guestCart = Cart::where("id", $guestCart->id)
                ->lockForUpdate()
                ->first();

            if ($guestCart->status !== Cart::ACTIVE) {
                throw new RuntimeException("Guest cart is no longer active.");
            }

            foreach ($guestCart->items as $item) {
                $existing = $customerCart
                    ->items()
                    ->where("product_id", $item->product_id)
                    ->where("product_variant_id", $item->product_variant_id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        "quantity" =>
                            (float) $existing->quantity + (float) $item->quantity,
                    ]);
                } else {
                    $customerCart
                        ->items()
                        ->create(
                            $item->only([
                                "product_id",
                                "tax_class_id",
                                "product_variant_id",
                                "sku",
                                "product_name",
                                "variant_name",
                                "quantity",
                                "unit_price",
                                "compare_price",
                                "discount_amount",
                                "tax_rate",
                                "tax_amount",
                                "line_subtotal",
                                "line_total",
                            ])
                        );
                }
            }

            $guestCart->update(["status" => Cart::MERGED]);
            return $this->recalculate($customerCart->fresh("items"));
        });
    }

    public function findByUuidOrFail(string $uuid): Cart
    {
        return $this->repository->findByUuidOrFail($uuid);
    }

    public function changeStatus(Cart $cart, string $status): Cart
    {
        return $this->repository->changeStatus($cart, $status);
    }
}
