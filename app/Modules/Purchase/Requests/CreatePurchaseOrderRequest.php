<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CreatePurchaseOrderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'supplier_uuid' => ['required','uuid','exists:suppliers,uuid'],
            'order_date' => ['required','date'],
            'expected_date' => ['nullable','date','after_or_equal:order_date'],
            'payment_terms_days' => ['nullable','integer','min:0','max:3650'],
            'currency_code' => ['nullable','string','size:3'],
            'shipping_amount' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string','max:5000'],
            'items' => ['required','array','min:1','max:500'],
            'items.*.product_uuid' => ['required','uuid','exists:products,uuid'],
            'items.*.product_variant_uuid' => ['nullable','uuid','exists:product_variants,uuid'],
            'items.*.unit_id' => ['nullable','integer','exists:units,id'],
            'items.*.sku' => ['nullable','string','max:100'],
            'items.*.description' => ['nullable','string','max:500'],
            'items.*.quantity' => ['required','numeric','gt:0'],
            'items.*.unit_price' => ['required','numeric','min:0'],
            'items.*.discount_amount' => ['nullable','numeric','min:0'],
            'items.*.tax_rate' => ['nullable','numeric','min:0','max:100'],
        ];
    }
}
