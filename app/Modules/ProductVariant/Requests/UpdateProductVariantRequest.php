<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variantUuid = $this->route("variantUuid");

        return [
            "name" => ["required", "string", "max:180"],

            "sku" => [
                "required",
                "string",
                "max:100",
                Rule::unique("product_variants", "sku")->ignore(
                    $variantUuid,
                    "uuid"
                ),
            ],

            "price" => ["nullable", "numeric", "min:0"],

            "compare_price" => ["nullable", "numeric", "min:0"],

            "cost_price" => ["nullable", "numeric", "min:0"],

            "stock_quantity" => ["nullable", "integer", "min:0"],

            "low_stock_threshold" => ["nullable", "integer", "min:0"],

            "is_default" => ["boolean"],

            "is_active" => ["boolean"],

            "sort_order" => ["nullable", "integer", "min:0"],

            "attribute_values" => ["required", "array", "min:1"],

            "attribute_values.*" => [
                "required",
                "uuid",
                "distinct",
                "exists:attribute_values,uuid",
            ],
        ];
    }
}
