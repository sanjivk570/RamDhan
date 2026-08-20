<?php

declare(strict_types=1);

namespace App\Modules\Tax\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxRateListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:100"],

            "per_page" => ["nullable", "integer", "min:1", "max:100"],

            "sort_by" => [
                "nullable",
                "string",
                "in:name,rate,country_code,state_code,is_active,priority,created_at",
            ],

            "sort_order" => ["nullable", "string", "in:asc,desc"],

            "filters" => ["nullable", "array"],

            "filters.tax_class_uuid" => ["nullable", "uuid"],

            "filters.name" => ["nullable", "string", "max:100"],

            "filters.country_code" => ["nullable", "string", "size:2"],

            "filters.state_code" => ["nullable", "string", "max:10"],

            "filters.is_active" => ["nullable", "boolean"],
        ];
    }
}
