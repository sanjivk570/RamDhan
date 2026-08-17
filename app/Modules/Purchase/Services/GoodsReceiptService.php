<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Services;

use App\Modules\Purchase\Contracts\InventoryStockInContract;
use App\Modules\Purchase\Models\GoodsReceipt;
use App\Modules\Purchase\Models\GoodsReceiptItem;
use App\Modules\Purchase\Models\PurchaseOrder;
use App\Modules\Purchase\Repositories\GoodsReceiptRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Illuminate\Support\Facades\Auth;

final class GoodsReceiptService
{
    public function __construct(
        private readonly GoodsReceiptRepository $repository,
        private readonly InventoryStockInContract $inventory
    ) {
    }

    public function list(array $filters)
    {
        return $this->repository->paginate($filters);
    }
    public function details(string $uuid): GoodsReceipt
    {
        return $this->repository->findByUuidOrFail($uuid);
    }

    public function create(array $data, ?int $userId): GoodsReceipt
    {
        return DB::transaction(function () use ($data, $userId): GoodsReceipt {
            $order = PurchaseOrder::with("items")
                ->where("uuid", $data["purchase_order_uuid"])
                ->firstOrFail();
            if (!$order->isReceivable()) {
                throw new ConflictHttpException(
                    "The purchase order is not available for receiving."
                );
            }

            $grn = GoodsReceipt::create([
                "purchase_order_id" => $order->id,
                "supplier_id" => $order->supplier_id,
                "received_by" => $userId,
                "status" => GoodsReceipt::DRAFT,
                "receipt_date" => $data["receipt_date"],
                "supplier_document_date" =>
                    $data["supplier_document_date"] ?? null,
                "supplier_document_number" =>
                    $data["supplier_document_number"] ?? null,
                "notes" => $data["notes"] ?? null,
            ]);

            foreach ($data["items"] as $row) {
                $poItem = $order->items->firstWhere(
                    "uuid",
                    $row["purchase_order_item_uuid"]
                );
                if (!$poItem) {
                    throw new UnprocessableEntityHttpException(
                        "Purchase order item does not belong to the selected purchase order."
                    );
                }
                $received = (float) $row["received_quantity"];
                $accepted = (float) $row["accepted_quantity"];
                $rejected = (float) ($row["rejected_quantity"] ?? 0);
                if (abs($accepted + $rejected - $received) > 0.0001) {
                    throw new UnprocessableEntityHttpException(
                        "Accepted + rejected quantity must equal received quantity."
                    );
                }
                $remaining = $poItem->remainingQuantity();
                if ($received > $remaining + 0.0001) {
                    throw new UnprocessableEntityHttpException(
                        "Received quantity exceeds remaining quantity for item {$poItem->uuid}."
                    );
                }
                $grn->items()->create([
                    "uuid" => (string) Str::uuid(),
                    "purchase_order_item_id" => $poItem->id,
                    "product_id" => $poItem->product_id,
                    "product_variant_id" => $poItem->product_variant_id,
                    "ordered_quantity" => $poItem->ordered_quantity,
                    "previously_received_quantity" =>
                        $poItem->received_quantity,
                    "received_quantity" => $received,
                    "accepted_quantity" => $accepted,
                    "rejected_quantity" => $rejected,
                    "unit_cost" => $row["unit_cost"] ?? $poItem->unit_price,
                    "batch_number" => $row["batch_number"] ?? null,
                    "expiry_date" => $row["expiry_date"] ?? null,
                    "notes" => $row["notes"] ?? null,
                ]);
            }
            return $this->repository->findByUuidOrFail($grn->uuid);
        });
    }

    public function post(string $uuid, ?int $userId): GoodsReceipt
    {
        return DB::transaction(function () use ($uuid, $userId): GoodsReceipt {
            $grn = $this->repository->findByUuidOrFail($uuid);
            if ($grn->status !== GoodsReceipt::DRAFT) {
                throw new ConflictHttpException(
                    "Only draft GRNs can be posted."
                );
            }
            if ($grn->items->isEmpty()) {
                throw new UnprocessableEntityHttpException(
                    "GRN must contain at least one item."
                );
            }
            $order = $grn
                ->purchaseOrder()
                ->with("items")
                ->firstOrFail();

            $postingItems = [];
            foreach ($grn->items as $item) {
                $poItem = $order->items->firstWhere(
                    "id",
                    $item->purchase_order_item_id
                );
                if (!$poItem) {
                    throw new UnprocessableEntityHttpException(
                        "Invalid purchase order item."
                    );
                }
                $remaining = $poItem->remainingQuantity();
                if ((float) $item->accepted_quantity > $remaining + 0.0001) {
                    throw new UnprocessableEntityHttpException(
                        "Accepted quantity exceeds remaining PO quantity."
                    );
                }
                $postingItems[] = $item->toArray();
            }

            $this->inventory->post($grn, $postingItems, $userId);

            foreach ($grn->items as $item) {
                $poItem = $order->items->firstWhere(
                    "id",
                    $item->purchase_order_item_id
                );
                $poItem->increment(
                    "received_quantity",
                    (float) $item->accepted_quantity
                );
                $poItem->increment(
                    "rejected_quantity",
                    (float) $item->rejected_quantity
                );
            }

            $order->refresh()->load("items");
            $allReceived = $order->items->every(
                fn($item) => $item->remainingQuantity() <= 0.0001
            );
            $order->update([
                "status" => $allReceived
                    ? PurchaseOrder::RECEIVED
                    : PurchaseOrder::PARTIALLY_RECEIVED,
            ]);
            $grn->update([
                "status" => GoodsReceipt::POSTED,
                "posted_by" => $userId,
                "posted_at" => now(),
            ]);
            return $this->repository->findByUuidOrFail($grn->uuid);
        });
    }

    public function void(
        string $uuid,
        ?int $userId,
        string $reason
    ): GoodsReceipt {
        return DB::transaction(function () use (
            $uuid,
            $userId,
            $reason
        ): GoodsReceipt {
            $grn = $this->repository->findByUuidOrFail($uuid);
            if ($grn->status !== GoodsReceipt::POSTED) {
                throw new ConflictHttpException(
                    "Only posted GRNs can be voided."
                );
            }

            $this->inventory->reverse($grn, $grn->items->toArray(), Auth::id());
            $order = $grn
                ->purchaseOrder()
                ->with("items")
                ->firstOrFail();
            foreach ($grn->items as $item) {
                $poItem = $order->items->firstWhere(
                    "id",
                    $item->purchase_order_item_id
                );
                if ($poItem) {
                    $poItem->decrement(
                        "received_quantity",
                        (float) $item->accepted_quantity
                    );
                    $poItem->decrement(
                        "rejected_quantity",
                        (float) $item->rejected_quantity
                    );
                }
            }

            $order->refresh()->load("items");
            $hasReceived = $order->items->contains(
                fn($item) => (float) $item->received_quantity > 0.0001
            );
            $order->update([
                "status" => $hasReceived
                    ? PurchaseOrder::PARTIALLY_RECEIVED
                    : PurchaseOrder::APPROVED,
            ]);
            $grn->update([
                "status" => GoodsReceipt::VOID,
                "voided_by" => $userId,
                "voided_at" => now(),
                "void_reason" => $reason,
            ]);
            return $this->repository->findByUuidOrFail($grn->uuid);
        });
    }
}
