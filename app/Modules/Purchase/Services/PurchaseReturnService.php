<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Services;
use App\Modules\Purchase\Models\PurchaseReturn;
use App\Modules\Purchase\Models\PurchaseReturnItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class PurchaseReturnService
{
    public function list(array $filters): LengthAwarePaginator
    {
        return PurchaseReturn::query()
            ->with("items")
            ->when(
                $filters["supplier_id"] ?? null,
                fn($q, $v) => $q->where("supplier_id", $v)
            )
            ->when(
                $filters["status"] ?? null,
                fn($q, $v) => $q->where("status", $v)
            )
            ->orderBy("created_at", "desc")
            ->paginate($filters["per_page"] ?? 20);
    }
    public function find(string $uuid): PurchaseReturn
    {
        return PurchaseReturn::with("items")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }
    public function create(array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($data) {
            $items = $data["items"];
            unset($data["items"]);
            $data["created_by"] = auth()->id();
            $data["status"] = "draft";
            $r = PurchaseReturn::create($data);
            $total = 0;
            foreach ($items as $row) {
                $qty = (float) $row["quantity"];
                $cost = (float) ($row["unit_cost"] ?? 0);
                $line = $qty * $cost;
                PurchaseReturnItem::create([
                    ...$row,
                    "purchase_return_id" => $r->id,
                    "line_total" => $line,
                ]);
                $total += $line;
            }
            $r->update(["total_amount" => $total]);
            return $r->load("items");
        });
    }
    public function post(string $uuid): PurchaseReturn
    {
        return DB::transaction(function () use ($uuid) {
            $r = $this->find($uuid);
            if ($r->status !== "draft") {
                throw new RuntimeException(
                    "Only draft purchase return can be posted."
                );
            }
            $r->update(["status" => "posted", "posted_at" => now()]);
            return $r->refresh()->load("items");
        });
    }
}
