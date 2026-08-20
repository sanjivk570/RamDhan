<?php
// namespace App\Modules\Purchase\Requests;
// use App\Core\Requests\BaseRequest;
// class CreatePurchaseInvoiceRequest extends BaseRequest
// {
//     public function rules(): array
//     {
//         return [
//             "supplier_id" => ["required", "integer"],
//             "purchase_order_id" => ["nullable", "integer"],
//             "goods_receipt_id" => ["nullable", "integer"],
//             "invoice_date" => ["required", "date"],
//             "due_date" => ["nullable", "date", "after_or_equal:invoice_date"],
//             "supplier_invoice_number" => ["nullable", "string", "max:100"],
//             "currency_code" => ["nullable", "string", "size:3"],
//             "shipping_amount" => ["nullable", "numeric", "min:0"],
//             "notes" => ["nullable", "string"],
//             "items" => ["required", "array", "min:1"],
//             "items.*.purchase_order_item_id" => ["nullable", "integer"],
//             "items.*.product_id" => ["required", "integer"],
//             "items.*.product_variant_id" => ["nullable", "integer"],
//             "items.*.unit_id" => ["nullable", "integer"],
//             "items.*.sku" => ["nullable", "string", "max:100"],
//             "items.*.description" => ["nullable", "string", "max:500"],
//             "items.*.quantity" => ["required", "numeric", "gt:0"],
//             "items.*.unit_price" => ["required", "numeric", "min:0"],
//             "items.*.discount_amount" => ["nullable", "numeric", "min:0"],
//             "items.*.tax_rate" => ["nullable", "numeric", "min:0"],
//             "items.*.tax_amount" => ["nullable", "numeric", "min:0"],
//         ];
//     }
// }

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CreatePurchaseInvoiceRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            /*
             * Invoice references
             */
            "supplier_id" => [
                "required",
                "integer",
                "exists:suppliers,id",
            ],

            "purchase_order_id" => [
                "nullable",
                "integer",
                "exists:purchase_orders,id",
            ],

            "goods_receipt_id" => [
                "nullable",
                "integer",
                "exists:goods_receipts,id",
            ],

            /*
             * Invoice details
             */
            "invoice_date" => [
                "required",
                "date",
            ],

            "due_date" => [
                "nullable",
                "date",
                "after_or_equal:invoice_date",
            ],

            "supplier_invoice_number" => [
                "nullable",
                "string",
                "max:100",
            ],

            "currency_code" => [
                "nullable",
                "string",
                "size:3",
            ],

            "shipping_amount" => [
                "nullable",
                "numeric",
                "min:0",
            ],

            "notes" => [
                "nullable",
                "string",
            ],

            /*
             * Invoice items
             */
            "items" => [
                "required",
                "array",
                "min:1",
            ],

            "items.*.purchase_order_item_id" => [
                "nullable",
                "integer",
                "exists:purchase_order_items,id",
            ],

            "items.*.product_id" => [
                "required",
                "integer",
                "exists:products,id",
            ],

            "items.*.product_variant_id" => [
                "nullable",
                "integer",
                "exists:product_variants,id",
            ],

            "items.*.unit_id" => [
                "nullable",
                "integer",
                "exists:units,id",
            ],

            "items.*.sku" => [
                "nullable",
                "string",
                "max:100",
            ],

            "items.*.description" => [
                "nullable",
                "string",
                "max:500",
            ],

            "items.*.quantity" => [
                "required",
                "numeric",
                "gt:0",
            ],

            "items.*.unit_price" => [
                "required",
                "numeric",
                "min:0",
            ],

            "items.*.discount_amount" => [
                "nullable",
                "numeric",
                "min:0",
            ],

            "items.*.tax_rate" => [
                "nullable",
                "numeric",
                "min:0",
            ],

            "items.*.tax_amount" => [
                "nullable",
                "numeric",
                "min:0",
            ],
        ];
    }
}
