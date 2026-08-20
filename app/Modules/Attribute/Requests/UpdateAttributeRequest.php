<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $uuid = $this->route("uuid");

        return [
            "name" => [
                "sometimes",
                "required",
                "string",
                "max:100",
                Rule::unique("attributes", "name")->ignore($uuid, "uuid"),
            ],

            "slug" => [
                "sometimes",
                "required",
                "string",
                "max:120",
                "alpha_dash",
                Rule::unique("attributes", "slug")->ignore($uuid, "uuid"),
            ],

            "type" => [
                "sometimes",
                "required",
                Rule::in(["select", "color", "text", "number"]),
            ],

            "sort_order" => ["sometimes", "integer", "min:0"],

            "is_active" => ["sometimes", "boolean"],
        ];
    }
}
