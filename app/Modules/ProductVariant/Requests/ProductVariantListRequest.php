<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:100"],

            "is_active" => ["nullable", "boolean"],

            "per_page" => ["nullable", "integer", "min:1", "max:100"],

            "sort_by" => [
                "nullable",
                "in:name,sku,price,stock_quantity,sort_order,created_at",
            ],

            "sort_order" => ["nullable", "in:asc,desc"],
        ];
    }
}
