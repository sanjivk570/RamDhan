<?php

declare(strict_types=1);

namespace App\Modules\Tax\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxClassListRequest extends FormRequest
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
                "in:name,code,is_active,sort_order,created_at",
            ],

            "sort_order" => ["nullable", "string", "in:asc,desc"],

            "filters" => ["nullable", "array"],

            "filters.name" => ["nullable", "string", "max:100"],

            "filters.code" => ["nullable", "string", "max:50"],

            "filters.is_active" => ["nullable", "boolean"],
        ];
    }
}
