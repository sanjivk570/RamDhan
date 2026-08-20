<?php

declare(strict_types=1);

namespace App\Modules\Unit\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:100", "unique:units,name"],

            "code" => [
                "required",
                "string",
                "max:30",
                "uppercase",
                "unique:units,code",
            ],

            "symbol" => ["nullable", "string", "max:20"],

            "decimal_places" => ["required", "integer", "min:0", "max:6"],

            "is_active" => ["sometimes", "boolean"],

            "sort_order" => ["sometimes", "integer", "min:0"],
        ];
    }
}
