<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:100"],

            "is_active" => ["nullable", "boolean"],

            "per_page" => ["nullable", "integer", "min:1", "max:100"],

            "sort_by" => [
                "nullable",
                "string",
                Rule::in([
                    "created_at",
                    "quantity",
                    "reserved_quantity",
                    "is_active",
                ]),
            ],

            "sort_order" => ["nullable", "string", Rule::in(["asc", "desc"])],
        ];
    }
}
