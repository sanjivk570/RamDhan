<?php

declare(strict_types=1);

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryListRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'search' => [
                'nullable',
                'string',
                'max:150',
            ],

            'is_active' => [
                'nullable',
                'in:0,1',
            ],

            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'sort_by' => [
                'nullable',
                'string',
                'in:name,slug,is_active,sort_order,created_at',
            ],

            'sort_order' => [
                'nullable',
                'in:asc,desc',
            ],

            'filters' => [
                'nullable',
                'array',
            ],

            'filters.name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'filters.parent_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'filters.parent_id' => [
                'nullable',
                'string',
                'max:100',
            ],

            'filters.description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'filters.slug' => [
                'nullable',
                'string',
                'max:20',
            ],

            'filters.status' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}