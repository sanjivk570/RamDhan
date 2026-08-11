<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Requests;

use App\Modules\Inventory\Models\InventoryTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockOutRequest extends FormRequest
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
                "gt:0",
                "max:9999999999.9999",
            ],

            "type" => [
                "required",
                "string",
                Rule::in([
                    InventoryTransaction::TYPE_SALE,
                    InventoryTransaction::TYPE_DAMAGE,
                    InventoryTransaction::TYPE_CANCELLATION,
                ]),
            ],

            "reference_type" => ["nullable", "string", "max:100"],

            "reference_id" => ["nullable", "string", "max:100"],

            "notes" => ["nullable", "string", "max:2000"],
        ];
    }
}
