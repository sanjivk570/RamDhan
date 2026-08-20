<?php

declare(strict_types=1);

namespace App\Modules\Unit\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitListRequest extends FormRequest
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
                "in:name,code,symbol,decimal_places,is_active,sort_order,created_at",
            ],

            "sort_order" => ["nullable", "string", "in:asc,desc"],

            "filters" => ["nullable", "array"],

            "filters.name" => ["nullable", "string", "max:100"],

            "filters.code" => ["nullable", "string", "max:30"],

            "filters.symbol" => ["nullable", "string", "max:20"],

            "filters.is_active" => ["nullable", "boolean"],
        ];
    }
}
