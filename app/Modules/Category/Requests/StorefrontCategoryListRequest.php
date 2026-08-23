<?php

declare(strict_types=1);

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate category listing requests for the public storefront.
 *
 * @package App\Modules\Category\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class StorefrontCategoryListRequest extends FormRequest
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
            'page' => ['nullable', 'integer', 'min:1'],

            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],

            'search' => ['nullable', 'string', 'max:150'],

            'sort_by' => ['nullable', 'string', 'in:name,slug,sort_order,created_at'],

            'sort_order' => ['nullable', 'in:asc,desc'],
        ];
    }
}