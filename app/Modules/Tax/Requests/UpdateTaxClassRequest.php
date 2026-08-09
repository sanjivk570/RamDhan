<?php

declare(strict_types=1);

namespace App\Modules\Tax\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaxClassRequest extends FormRequest
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
                "required",
                "string",
                "max:100",
                Rule::unique("tax_classes", "name")->ignore($uuid, "uuid"),
            ],

            "code" => [
                "required",
                "string",
                "max:50",
                "uppercase",
                Rule::unique("tax_classes", "code")->ignore($uuid, "uuid"),
            ],

            "description" => ["nullable", "string"],

            "is_active" => ["sometimes", "boolean"],

            "sort_order" => ["sometimes", "integer", "min:0"],
        ];
    }
}
