<?php

declare(strict_types=1);

namespace App\Modules\Tax\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "tax_class_uuid" => ["required", "uuid", "exists:tax_classes,uuid"],

            "name" => ["required", "string", "max:100"],

            "rate" => ["required", "numeric", "min:0", "max:100"],

            "country_code" => ["sometimes", "string", "size:2"],

            "state_code" => ["nullable", "string", "max:10"],

            "is_active" => ["sometimes", "boolean"],

            "priority" => ["sometimes", "integer", "min:0"],
        ];
    }
}
