<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Services;
use App\Modules\SalesInvoice\Models\SalesInvoice;
use App\Modules\Order\Models\Order;
use Illuminate\Support\Facades\DB;
final class SalesInvoiceService
{
    public function createForOrder(Order $order): SalesInvoice
    {
        return DB::transaction(function () use ($order) {
            $existing = SalesInvoice::where("order_id", $order->id)->first();
            if ($existing) {
                return $existing->load("items");
            }
            $i = SalesInvoice::create([
                "order_id" => $order->id,
                "customer_id" => $order->customer_id,
                "status" => "issued",
                "invoice_date" => now()->toDateString(),
                "currency_code" => $order->currency_code,
                "subtotal" => $order->subtotal,
                "discount_amount" => $order->discount_amount,
                "tax_amount" => $order->tax_amount,
                "shipping_amount" => $order->shipping_amount,
                "grand_total" => $order->grand_total,
                "paid_amount" => 0,
                "due_amount" => $order->grand_total,
                "billing_address" => $order->billing_address,
            ]);
            foreach ($order->items as $oi) {
                $i->items()->create([
                    "order_item_id" => $oi->id,
                    "product_id" => $oi->product_id,
                    "product_variant_id" => $oi->product_variant_id,
                    "sku" => $oi->sku,
                    "description" =>
                        $oi->product_name .
                        ($oi->variant_name ? " - " . $oi->variant_name : ""),
                    "quantity" => $oi->quantity,
                    "unit_price" => $oi->unit_price,
                    "discount_amount" => $oi->discount_amount,
                    "tax_rate" => $oi->tax_rate,
                    "tax_amount" => $oi->tax_amount,
                    "line_subtotal" => $oi->line_subtotal,
                    "line_total" => $oi->line_total,
                ]);
            }
            return $i->load("items");
        });
    }
    public function customerList(int $customerId)
    {
        return SalesInvoice::where("customer_id", $customerId)
            ->latest()
            ->paginate(20);
    }
    public function customerShow(int $customerId, string $uuid): SalesInvoice
    {
        return SalesInvoice::with("items")
            ->where("customer_id", $customerId)
            ->where("uuid", $uuid)
            ->firstOrFail();
    }
    public function adminList(array $f)
    {
        return SalesInvoice::query()
            ->when($f["status"] ?? null, fn($q, $v) => $q->where("status", $v))
            ->latest()
            ->paginate($f["per_page"] ?? 20);
    }
    public function adminShow(string $uuid): SalesInvoice
    {
        return SalesInvoice::with("items")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }
}
