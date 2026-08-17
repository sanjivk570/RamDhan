<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Services;
use App\Modules\Purchase\Models\PurchaseInvoice;
use App\Modules\Purchase\Models\PurchasePayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class PurchasePaymentService
{
    public function list(array $filters): LengthAwarePaginator
    {
        return PurchasePayment::query()
            ->when(
                $filters["supplier_id"] ?? null,
                fn($q, $v) => $q->where("supplier_id", $v)
            )
            ->when(
                $filters["invoice_uuid"] ?? null,
                fn($q, $v) => $q->whereHas(
                    "invoice",
                    fn($x) => $x->where("uuid", $v)
                )
            )
            ->when(
                $filters["payment_method"] ?? null,
                fn($q, $v) => $q->where("payment_method", $v)
            )
            ->orderBy("payment_date", "desc")
            ->paginate($filters["per_page"] ?? 20);
    }
    public function find(string $uuid): PurchasePayment
    {
        return PurchasePayment::with("invoice")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }
    public function create(array $data): PurchasePayment
    {
        return DB::transaction(function () use ($data) {
            $invoice = PurchaseInvoice::where("uuid", $data["invoice_uuid"])
                ->lockForUpdate()
                ->firstOrFail();
            if ($invoice->status !== "posted") {
                throw new RuntimeException(
                    "Payment is allowed only for a posted purchase invoice."
                );
            }
            $amount = (float) $data["amount"];
            $due =
                (float) $invoice->grand_total - (float) $invoice->paid_amount;
            if ($amount <= 0 || $amount > $due + 0.0001) {
                throw new RuntimeException(
                    "Payment amount exceeds invoice due amount."
                );
            }
            unset($data["invoice_uuid"]);
            $payment = PurchasePayment::create([
                ...$data,
                "supplier_id" => $invoice->supplier_id,
                "purchase_invoice_id" => $invoice->id,
                "created_by" => auth()->id(),
                "status" => "posted",
            ]);
            $paid = (float) $invoice->paid_amount + $amount;
            $invoice->update([
                "paid_amount" => $paid,
                "due_amount" => max(0, (float) $invoice->grand_total - $paid),
                "status" =>
                    $paid + 0.0001 >= (float) $invoice->grand_total
                        ? "paid"
                        : "posted",
            ]);
            return $payment->load("invoice");
        });
    }
}
