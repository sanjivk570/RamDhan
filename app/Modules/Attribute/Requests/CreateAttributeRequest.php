<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => [
                "required",
                "string",
                "max:100",
                "unique:attributes,name",
            ],

            "slug" => [
                "required",
                "string",
                "max:120",
                "alpha_dash",
                "unique:attributes,slug",
            ],

            "type" => [
                "required",
                Rule::in(["select", "color", "text", "number"]),
            ],

            "sort_order" => ["nullable", "integer", "min:0"],

            "is_active" => ["nullable", "boolean"],
        ];
    }
}
