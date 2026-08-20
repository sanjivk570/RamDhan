<?php

declare(strict_types=1);

namespace App\Modules\Attribute\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:100"],

            "filters" => ["nullable", "array"],

            "filters.name" => ["nullable", "string", "max:100"],

            "filters.type" => ["nullable", "string", "max:30"],

            "filters.status" => ["nullable", "in:0,1"],

            "per_page" => ["nullable", "integer", "min:1", "max:100"],

            "sort_by" => [
                "nullable",
                Rule::in(["name", "slug", "type", "sort_order", "created_at"]),
            ],

            "sort_order" => ["nullable", Rule::in(["asc", "desc"])],
        ];
    }
}
