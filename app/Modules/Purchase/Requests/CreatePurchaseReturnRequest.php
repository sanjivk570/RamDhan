<?php
// namespace App\Modules\Purchase\Requests;
// use App\Core\Requests\BaseRequest;
// class CreatePurchaseReturnRequest extends BaseRequest
// {
//     public function rules(): array
//     {
//         return [
//             "supplier_id" => ["required", "integer"],
//             "goods_receipt_id" => ["nullable", "integer"],
//             "return_date" => ["required", "date"],
//             "currency_code" => ["nullable", "string", "size:3"],
//             "reason" => ["nullable", "string"],
//             "items" => ["required", "array", "min:1"],
//             "items.*.product_id" => ["required", "integer"],
//             "items.*.product_variant_id" => ["nullable", "integer"],
//             "items.*.quantity" => ["required", "numeric", "gt:0"],
//             "items.*.unit_cost" => ["required", "numeric", "min:0"],
//             "items.*.reason" => ["nullable", "string"],
//         ];
//     }
// }

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;

class CreatePurchaseReturnRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            /*
             * Purchase return references
             */
            "supplier_id" => [
                "required",
                "integer",
                "exists:suppliers,id",
            ],

            "goods_receipt_id" => [
                "nullable",
                "integer",
                "exists:goods_receipts,id",
            ],

            /*
             * Return details
             */
            "return_date" => [
                "required",
                "date",
            ],

            "currency_code" => [
                "nullable",
                "string",
                "size:3",
            ],

            "reason" => [
                "nullable",
                "string",
            ],

            /*
             * Return items
             */
            "items" => [
                "required",
                "array",
                "min:1",
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

            "items.*.quantity" => [
                "required",
                "numeric",
                "gt:0",
            ],

            "items.*.unit_cost" => [
                "required",
                "numeric",
                "min:0",
            ],

            "items.*.reason" => [
                "nullable",
                "string",
            ],
        ];
    }
}