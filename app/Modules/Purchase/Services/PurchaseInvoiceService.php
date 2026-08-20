<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Services;

use App\Modules\Purchase\Models\PurchaseInvoice;
use App\Modules\Purchase\Models\PurchaseInvoiceItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

final class PurchaseInvoiceService
{
    public function list(array $filters): LengthAwarePaginator
    {
        return PurchaseInvoice::query()
            ->with("items")
            ->when(
                $filters["search"] ?? null,
                fn($q, $s) => $q->where(
                    fn($x) => $x
                        ->where("invoice_number", "like", "%$s%")
                        ->orWhere("supplier_invoice_number", "like", "%$s%")
                )
            )
            ->when(
                $filters["supplier_id"] ?? null,
                fn($q, $v) => $q->where("supplier_id", $v)
            )
            ->when(
                $filters["status"] ?? null,
                fn($q, $v) => $q->where("status", $v)
            )
            ->orderBy(
                $filters["sort_by"] ?? "created_at",
                $filters["sort_order"] ?? "desc"
            )
            ->paginate($filters["per_page"] ?? 20);
    }
    public function find(string $uuid): PurchaseInvoice
    {
        return PurchaseInvoice::with(["items", "payments"])
            ->where("uuid", $uuid)
            ->firstOrFail();
    }
    public function create(array $data): PurchaseInvoice
    {
        return DB::transaction(function () use ($data) {
            $items = $data["items"];
            unset($data["items"]);
            $data["created_by"] = auth()->id();
            $data["status"] = "draft";
            $invoice = PurchaseInvoice::create($data);
            $subtotal = $discount = $tax = $total = 0.0;
            foreach ($items as $row) {
                $qty = (float) $row["quantity"];
                $price = (float) ($row["unit_price"] ?? 0);
                $disc = (float) ($row["discount_amount"] ?? 0);
                $base = max(0, $qty * $price - $disc);
                $taxAmount =
                    (float) ($row["tax_amount"] ??
                        round(
                            ($base * ((float) ($row["tax_rate"] ?? 0))) / 100,
                            2
                        ));
                $line = $base + $taxAmount;
                PurchaseInvoiceItem::create([
                    ...$row,
                    "purchase_invoice_id" => $invoice->id,
                    "discount_amount" => $disc,
                    "tax_amount" => $taxAmount,
                    "line_subtotal" => $base,
                    "line_total" => $line,
                ]);
                $subtotal += $base;
                $discount += $disc;
                $tax += $taxAmount;
                $total += $line;
            }
            $invoice->update([
                "subtotal" => $subtotal,
                "discount_amount" => $discount,
                "tax_amount" => $tax,
                "grand_total" =>
                    $total + (float) ($invoice->shipping_amount ?? 0),
                "due_amount" => $total,
            ]);
            return $invoice->load("items");
        });
    }
    public function post(string $uuid): PurchaseInvoice
    {
        return DB::transaction(function () use ($uuid) {
            $invoice = $this->find($uuid);
            if ($invoice->status !== "draft") {
                throw new RuntimeException(
                    "Only draft purchase invoice can be posted."
                );
            }
            $invoice->update([
                "status" => "posted",
                "posted_at" => now(),
                "due_amount" => max(
                    0,
                    (float) $invoice->grand_total -
                        (float) $invoice->paid_amount
                ),
            ]);
            return $invoice->refresh()->load("items");
        });
    }
}
