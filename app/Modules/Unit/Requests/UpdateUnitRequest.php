<?php

declare(strict_types=1);

namespace App\Modules\Unit\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
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
                Rule::unique("units", "name")->ignore($uuid, "uuid"),
            ],

            "code" => [
                "required",
                "string",
                "max:30",
                "uppercase",
                Rule::unique("units", "code")->ignore($uuid, "uuid"),
            ],

            "symbol" => ["nullable", "string", "max:20"],

            "decimal_places" => ["required", "integer", "min:0", "max:6"],

            "is_active" => ["sometimes", "boolean"],

            "sort_order" => ["sometimes", "integer", "min:0"],
        ];
    }
}
