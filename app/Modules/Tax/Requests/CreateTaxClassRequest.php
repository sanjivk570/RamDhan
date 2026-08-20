<?php

declare(strict_types=1);

namespace App\Modules\Tax\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaxClassRequest extends FormRequest
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
                "unique:tax_classes,name",
            ],

            "code" => [
                "required",
                "string",
                "max:50",
                "uppercase",
                "unique:tax_classes,code",
            ],

            "description" => ["nullable", "string"],

            "is_active" => ["sometimes", "boolean"],

            "sort_order" => ["sometimes", "integer", "min:0"],
        ];
    }
}
