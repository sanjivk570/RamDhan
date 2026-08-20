<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate product creation requests.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CreateProductRequest extends FormRequest
{

    /**
     * Determine whether the user is authorized
     * to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255"],

            "slug" => ["required", "string", "max:255", "unique:products,slug"],

            "sku" => ["required", "string", "max:100", "unique:products,sku"],

            "unit_id" => ["nullable", "numeric"],

            "tax_class_id" => ["nullable", "numeric"],

            "description" => ["nullable", "string"],

            "short_description" => ["nullable", "string", "max:500"],

            "price" => ["required", "numeric", "min:0"],

            "compare_price" => ["nullable", "numeric", "min:0"],

            'cost_price' => ['nullable', 'numeric', 'min:0'],

            "stock_quantity" => ["required", "integer", "min:0"],

            "is_active" => ["nullable", "boolean"],

            "is_featured" => ["nullable", "boolean"],

            "sort_order" => ["nullable", "integer", "min:0"],

            "categories" => ["required", "array", "min:1"],

            "categories.*" => ["uuid", "exists:categories,uuid"],
        ];
    }
}
