<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "value" => ["sometimes", "required", "string", "max:100"],

            "slug" => [
                "sometimes",
                "required",
                "string",
                "max:120",
                "alpha_dash",
            ],

            "display_value" => ["nullable", "string", "max:100"],

            "sort_order" => ["sometimes", "integer", "min:0"],

            "is_active" => ["sometimes", "boolean"],
        ];
    }
}
