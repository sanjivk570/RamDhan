<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate product image creation requests.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class CreateProductImageRequest extends FormRequest
{
    /**
     * CreateProductImageRequest constructor.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Determine whether the user is authorized
     * to make this request.
     *
     * @return bool
     */
    public function rules(): array
    {
        return [
            "image"      => ["required", "image", "mimes:jpg,jpeg,png,webp", "max:5120"],
            "alt_text"   => ["nullable", "string", "max:255"],
            "sort_order" => ["nullable", "integer", "min:0"],
            "is_primary" => ["nullable", "boolean"],
        ];
    }
}
