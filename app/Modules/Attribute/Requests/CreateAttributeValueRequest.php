<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "value" => ["required", "string", "max:100"],

            "slug" => ["required", "string", "max:120", "alpha_dash"],

            "display_value" => ["nullable", "string", "max:100"],

            "sort_order" => ["nullable", "integer", "min:0"],

            "is_active" => ["nullable", "boolean"],
        ];
    }
}
