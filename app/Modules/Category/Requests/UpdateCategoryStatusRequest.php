<?php

declare(strict_types=1);

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'boolean',
            ],
        ];
    }
}