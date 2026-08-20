<?php

declare(strict_types=1);

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryUuid = $this->route('uuid');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:150',
            ],

            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')
                    ->ignore($categoryUuid, 'uuid'),
            ],

            'parent_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:5000',
            ],

            'image' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}