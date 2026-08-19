<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Services;
use App\Modules\SalesReturn\Models\SalesReturn;
use App\Modules\Order\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
final class SalesReturnService
{
    public function create(array $data, int $customerId): SalesReturn
    {
        return DB::transaction(function () use ($data, $customerId) {
            $o = Order::where("uuid", $data["order_uuid"])
                ->where("customer_id", $customerId)
                ->with("items")
                ->firstOrFail();
            if (!in_array($o->status, ["delivered", "completed"], true)) {
                throw new RuntimeException(
                    "Return is allowed only for delivered/completed orders."
                );
            }
            $r = SalesReturn::create([
                "order_id" => $o->id,
                "customer_id" => $customerId,
                "status" => "requested",
                "refund_status" => "pending",
                "reason" => $data["reason"] ?? null,
                "customer_note" => $data["customer_note"] ?? null,
            ]);
            $total = 0;
            foreach ($data["items"] as $row) {
                $oi = $o->items->first(
                    fn($x) => $x->uuid === $row["order_item_uuid"]
                );
                if (!$oi) {
                    throw new RuntimeException("Order item not found.");
                }
                $line = (float) $oi->unit_price * (float) $row["quantity"];
                $r->items()->create([
                    "order_item_id" => $oi->id,
                    "product_id" => $oi->product_id,
                    "product_variant_id" => $oi->product_variant_id,
                    "quantity" => $row["quantity"],
                    "unit_price" => $oi->unit_price,
                    "line_total" => $line,
                    "reason" => $row["reason"] ?? null,
                ]);
                $total += $line;
            }
            $r->update(["total_amount" => $total]);
            return $r->load("items");
        });
    }
    public function customerList(int $id)
    {
        return SalesReturn::where("customer_id", $id)
            ->latest()
            ->paginate(20);
    }
    public function customerShow(int $id, string $uuid): SalesReturn
    {
        return SalesReturn::with("items")
            ->where("customer_id", $id)
            ->where("uuid", $uuid)
            ->firstOrFail();
    }
    public function adminList(array $f)
    {
        return SalesReturn::query()
            ->when($f["status"] ?? null, fn($q, $v) => $q->where("status", $v))
            ->latest()
            ->paginate($f["per_page"] ?? 20);
    }
    public function process(
        SalesReturn $r,
        string $action,
        ?string $note,
        int $userId
    ): SalesReturn {
        if ($action === "approve") {
            if ($r->status !== "requested") {
                throw new RuntimeException("Return is not awaiting approval.");
            }
            $r->update([
                "status" => "approved",
                "admin_note" => $note,
                "processed_by" => $userId,
                "approved_at" => now(),
            ]);
        } elseif ($action === "reject") {
            if ($r->status !== "requested") {
                throw new RuntimeException("Return is not awaiting approval.");
            }
            $r->update([
                "status" => "rejected",
                "admin_note" => $note,
                "processed_by" => $userId,
                "rejected_at" => now(),
            ]);
        } else {
            if ($r->status !== "approved") {
                throw new RuntimeException(
                    "Return must be approved before receiving."
                );
            }
            $r->load("items");
            DB::transaction(function () use ($r) {
                foreach ($r->items as $item) {
                    $q = DB::table("inventory_stocks")->where(
                        "product_id",
                        $item->product_id
                    );
                    if ($item->product_variant_id) {
                        $q->where(
                            "product_variant_id",
                            $item->product_variant_id
                        );
                    }
                    $stock = $q->lockForUpdate()->first();
                    if (!$stock) {
                        DB::table("inventory_stocks")->insert([
                            "uuid" => (string) Str::uuid(),
                            "product_id" => $item->product_id,
                            "product_variant_id" => $item->product_variant_id,
                            "quantity" => $item->quantity,
                            "reserved_quantity" => 0,
                            "is_active" => true,
                            "created_at" => now(),
                            "updated_at" => now(),
                        ]);
                        continue;
                    }
                    $after = (float) $stock->quantity + (float) $item->quantity;
                    DB::table("inventory_stocks")
                        ->where("id", $stock->id)
                        ->update(["quantity" => $after, "updated_at" => now()]);
                    if (
                        DB::getSchemaBuilder()->hasTable(
                            "inventory_transactions"
                        )
                    ) {
                        DB::table("inventory_transactions")->insert([
                            "uuid" => (string) Str::uuid(),
                            "inventory_stock_id" => $stock->id,
                            "product_id" => $item->product_id,
                            "product_variant_id" => $item->product_variant_id,
                            "type" => "sales_return",
                            "quantity" => $item->quantity,
                            "quantity_before" => $stock->quantity,
                            "quantity_after" => $after,
                            "reference_type" => "sales_return",
                            "reference_id" => $r->uuid,
                            "notes" => "Stock received from customer return",
                            "created_at" => now(),
                            "updated_at" => now(),
                        ]);
                    }
                }
                $r->update([
                    "status" => "received",
                    "refund_status" => "pending",
                ]);
            });
        }
        return $r->fresh("items");
    }
}
