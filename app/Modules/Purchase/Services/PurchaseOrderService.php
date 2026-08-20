<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Services;

use App\Modules\Purchase\Models\PurchaseOrder;
use App\Modules\Purchase\Models\PurchaseOrderItem;
use App\Modules\Purchase\Repositories\PurchaseOrderRepository;
use App\Modules\Supplier\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class PurchaseOrderService
{
    public function __construct(
        private readonly PurchaseOrderRepository $repository
    ) {
    }

    public function list(array $filters)
    {
        return $this->repository->paginate($filters);
    }
    public function details(string $uuid): PurchaseOrder
    {
        return $this->repository->findByUuidOrFail($uuid);
    }

    public function create(array $data, ?int $userId): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $userId): PurchaseOrder {
            $supplier = Supplier::where(
                "uuid",
                $data["supplier_uuid"]
            )->firstOrFail();
            if (!$supplier->is_active) {
                throw new ConflictHttpException("The supplier is inactive.");
            }

            $items = $data["items"];
            unset($data["supplier_uuid"], $data["items"]);
            $data["supplier_id"] = $supplier->id;
            $data["created_by"] = $userId;
            $data["status"] = PurchaseOrder::DRAFT;
            $data["payment_terms_days"] ??= $supplier->payment_terms_days;
            $data["currency_code"] = strtoupper(
                $data["currency_code"] ?? "INR"
            );
            $data["shipping_amount"] = $data["shipping_amount"] ?? 0;

            $order = $this->repository->create($data);
            $this->replaceItems($order, $items);
            return $this->repository->findByUuidOrFail($order->uuid);
        });
    }

    public function update(string $uuid, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($uuid, $data): PurchaseOrder {
            $order = $this->repository->findByUuidOrFail($uuid);
            if (!$order->isEditable()) {
                throw new ConflictHttpException(
                    "Only draft purchase orders can be edited."
                );
            }

            $items = $data["items"];
            $supplier = Supplier::where(
                "uuid",
                $data["supplier_uuid"]
            )->firstOrFail();
            if (!$supplier->is_active) {
                throw new ConflictHttpException("The supplier is inactive.");
            }
            unset($data["supplier_uuid"], $data["items"]);
            $data["supplier_id"] = $supplier->id;
            if (isset($data["shipping_amount"])) {
                $data["shipping_amount"] = (float) $data["shipping_amount"];
            }
            $this->repository->update($order, $data);
            $order->items()->delete();
            $this->replaceItems($order, $items);
            return $this->repository->findByUuidOrFail($order->uuid);
        });
    }

    public function submit(string $uuid): PurchaseOrder
    {
        $order = $this->repository->findByUuidOrFail($uuid);
        if ($order->status !== PurchaseOrder::DRAFT) {
            throw new ConflictHttpException(
                "Only draft purchase orders can be submitted."
            );
        }
        if ($order->items->isEmpty()) {
            throw new UnprocessableEntityHttpException(
                "Purchase order must contain at least one item."
            );
        }
        $order->update([
            "status" => PurchaseOrder::SUBMITTED,
            "submitted_at" => now(),
        ]);
        return $order->refresh()->load(["supplier", "items"]);
    }

    public function approve(string $uuid, ?int $userId): PurchaseOrder
    {
        $order = $this->repository->findByUuidOrFail($uuid);
        if ($order->status !== PurchaseOrder::SUBMITTED) {
            throw new ConflictHttpException(
                "Only submitted purchase orders can be approved."
            );
        }
        $order->update([
            "status" => PurchaseOrder::APPROVED,
            "approved_by" => $userId,
            "approved_at" => now(),
        ]);
        return $order->refresh()->load(["supplier", "items"]);
    }

    public function cancel(
        string $uuid,
        ?int $userId,
        ?string $reason
    ): PurchaseOrder {
        $order = $this->repository->findByUuidOrFail($uuid);
        if (
            in_array(
                $order->status,
                [
                    PurchaseOrder::PARTIALLY_RECEIVED,
                    PurchaseOrder::RECEIVED,
                    PurchaseOrder::CLOSED,
                    PurchaseOrder::CANCELLED,
                ],
                true
            )
        ) {
            throw new ConflictHttpException(
                "This purchase order cannot be cancelled in its current state."
            );
        }
        $order->update([
            "status" => PurchaseOrder::CANCELLED,
            "cancelled_by" => $userId,
            "cancelled_at" => now(),
            "cancellation_reason" => $reason,
        ]);
        return $order->refresh()->load(["supplier", "items"]);
    }

    private function replaceItems(PurchaseOrder $order, array $items): void
    {
        $subtotal = 0.0;
        $discount = 0.0;
        $tax = 0.0;
        foreach ($items as $row) {
            $quantity = (float) $row["quantity"];
            $unitPrice = (float) $row["unit_price"];
            $lineGross = $quantity * $unitPrice;
            $lineDiscount = min(
                $lineGross,
                (float) ($row["discount_amount"] ?? 0)
            );
            $taxable = $lineGross - $lineDiscount;
            $taxRate = (float) ($row["tax_rate"] ?? 0);
            $lineTax = round(($taxable * $taxRate) / 100, 2);
            $lineTotal = $taxable + $lineTax;

            $productId = (int) DB::table("products")
                ->where("uuid", $row["product_uuid"])
                ->value("id");
            if (!$productId) {
                throw new UnprocessableEntityHttpException("Invalid product.");
            }
            $variantId = null;
            if (!empty($row["product_variant_uuid"])) {
                $variantId = DB::table("product_variants")
                    ->where("uuid", $row["product_variant_uuid"])
                    ->value("id");
                if (!$variantId) {
                    throw new UnprocessableEntityHttpException(
                        "Invalid product variant."
                    );
                }
            }

            $item = new PurchaseOrderItem([
                "uuid" => (string) Str::uuid(),
                "product_id" => $productId,
                "product_variant_id" => $variantId,
                "unit_id" => $row["unit_id"] ?? null,
                "sku" => $row["sku"] ?? null,
                "description" => $row["description"] ?? null,
                "ordered_quantity" => $quantity,
                "unit_price" => $unitPrice,
                "discount_amount" => $lineDiscount,
                "tax_rate" => $taxRate,
                "tax_amount" => $lineTax,
                "line_subtotal" => $taxable,
                "line_total" => $lineTotal,
            ]);
            $order->items()->save($item);
            $subtotal += $taxable;
            $discount += $lineDiscount;
            $tax += $lineTax;
        }
        $shipping = (float) $order->shipping_amount;
        $order->update([
            "subtotal" => round($subtotal, 2),
            "discount_amount" => round($discount, 2),
            "tax_amount" => round($tax, 2),
            "grand_total" => round($subtotal + $tax + $shipping, 2),
        ]);
    }
}
