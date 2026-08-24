<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate product listing and filtering requests for the public storefront.
 *
 * @package App\Modules\Product\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class StorefrontProductListRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
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
            "search" => ["nullable", "string", "max:255"],

            "category" => ["nullable", "string", "max:255"],

            "filters" => ["nullable", "array"],

            "filters.name" => ["nullable", "string", "max:255"],

            "filters.slug" => ["nullable", "string", "max:255"],

            "filters.category" => ["nullable", "uuid"],

            "filters.is_featured" => ["nullable", "boolean"],

            "filters.min_price" => ["nullable", "numeric", "min:0"],

            "filters.max_price" => ["nullable", "numeric", "min:0"],

            "sort_by" => ["nullable", "string", "in:name,price,is_featured,sort_order,created_at,updated_at"],

            "sort_order" => ["nullable", "string", "in:asc,desc"],

            "per_page" => ["nullable", "integer", "min:1", "max:100"],
        ];
    }
}