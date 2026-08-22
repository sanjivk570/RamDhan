<?php

declare(strict_types=1);

namespace App\Modules\Shipment\Services;
use App\Modules\Shipment\Models\Shipment;
use App\Modules\Shipment\Repositories\ShipmentRepository;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
final class ShipmentService
{
    public function __construct(
        private readonly ShipmentRepository $repository
    ) {
    }
    public function create(array $data, int $userId): Shipment
    {
        return DB::transaction(function () use ($data, $userId) {
            $o = Order::where("uuid", $data["order_uuid"])
                ->with("items")
                ->lockForUpdate()
                ->firstOrFail();
            if (in_array($o->status, ["cancelled", "completed"], true)) {
                throw new RuntimeException("Order is not shippable.");
            }
            $s = Shipment::create([
                "order_id" => $o->id,
                "carrier" => $data["carrier"] ?? null,
                "service" => $data["service"] ?? null,
                "tracking_number" => $data["tracking_number"] ?? null,
                "tracking_url" => $data["tracking_url"] ?? null,
                "shipping_address" => $o->shipping_address,
                "created_by" => $userId,
                "notes" => $data["notes"] ?? null,
                "status" => "pending",
            ]);
            foreach ($data["items"] as $row) {
                $oi = $o->items->first(
                    fn($x) => $x->uuid === $row["order_item_uuid"]
                );
                if (!$oi) {
                    throw new RuntimeException("Order item not found.");
                }
                $s->items()->create([
                    "order_item_id" => $oi->id,
                    "product_id" => $oi->product_id,
                    "product_variant_id" => $oi->product_variant_id,
                    "quantity" => $row["quantity"],
                ]);
            }
            return $s->load("items");
        });
    }
    public function update(Shipment $s, array $data): Shipment
    {
        $s->update(array_filter($data, fn($v) => $v !== null));
        return $s->fresh("items");
    }
    public function listForCustomer(int $customerId)
    {
        return Shipment::whereHas(
            "order",
            fn($q) => $q->where("customer_id", $customerId)
        )
            ->latest()
            ->paginate(20);
    }
    public function showForCustomer(int $customerId, string $uuid): Shipment
    {
        return Shipment::with("items")
            ->where("uuid", $uuid)
            ->whereHas("order", fn($q) => $q->where("customer_id", $customerId))
            ->firstOrFail();
    }
    public function listAdmin(array $f)
    {
        return $this->repository->paginate($f);
    }
    public function showAdmin(string $uuid): Shipment
    {
        return $this->repository->findByUuidOrFail($uuid);
    }
    public function ship(Shipment $s): Shipment
    {
        return DB::transaction(function () use ($s) {
            // Validate current shipment status - can only ship from pending/created status
            if (!in_array($s->status, ['pending', 'created'], true)) {
                throw new RuntimeException(
                    'Shipment cannot be shipped from ' . $s->status . ' status. ' .
                    'Only pending or created shipments can be shipped.'
                );
            }

            $s->load("items", "order");
            foreach ($s->items as $si) {
                $q = DB::table("inventory_stocks")->where(
                    "product_id",
                    $si->product_id
                );
                if ($si->product_variant_id) {
                    $q->where("product_variant_id", $si->product_variant_id);
                }
                $stock = $q->lockForUpdate()->firstOrFail();
                if ((float) $stock->quantity < (float) $si->quantity) {
                    throw new RuntimeException("Insufficient on-hand stock.");
                }
                DB::table("inventory_stocks")
                    ->where("id", $stock->id)
                    ->update([
                        "quantity" =>
                            (float) $stock->quantity - (float) $si->quantity,
                        "reserved_quantity" => max(
                            0,
                            (float) $stock->reserved_quantity -
                                (float) $si->quantity
                        ),
                        "updated_at" => now(),
                    ]);
                if (Schema::hasTable("inventory_transactions")) {
                    DB::table("inventory_transactions")->insert([
                        "uuid" => (string) Str::uuid(),
                        "inventory_stock_id" => $stock->id,
                        "product_id" => $si->product_id,
                        "product_variant_id" => $si->product_variant_id,
                        "type" => "sales_ship",
                        "quantity" => $si->quantity,
                        "quantity_before" => $stock->quantity,
                        "quantity_after" =>
                            (float) $stock->quantity - (float) $si->quantity,
                        "reference_type" => "shipment",
                        "reference_id" => $s->uuid,
                        "notes" => "Stock consumed by shipment",
                        "created_at" => now(),
                        "updated_at" => now(),
                    ]);
                }
            }
            $s->update(["status" => "shipped", "shipped_at" => now()]);
            $order = $s->order->load("items");
            $shipped = \App\Modules\Shipment\Models\ShipmentItem::whereHas(
                "shipment",
                fn($q) => $q
                    ->where("order_id", $order->id)
                    ->whereIn("status", ["shipped", "delivered"])
            )
                ->get()
                ->groupBy("order_item_id")
                ->map(fn($rows) => (float) $rows->sum("quantity"));
            $partial = false;
            foreach ($order->items as $oi) {
                if ((float) ($shipped[$oi->id] ?? 0) < (float) $oi->quantity) {
                    $partial = true;
                    break;
                }
            }
            $order->update([
                "status" => Order::SHIPPED,
                "fulfillment_status" => $partial
                    ? "partially_fulfilled"
                    : "fulfilled",
            ]);
            return $s->fresh("items");
        });
    }
}
