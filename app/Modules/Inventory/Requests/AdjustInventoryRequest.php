<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "quantity" => [
                "required",
                "numeric",
                "min:0",
                "max:9999999999.9999",
            ],

            "notes" => ["nullable", "string", "max:2000"],
        ];
    }
}
