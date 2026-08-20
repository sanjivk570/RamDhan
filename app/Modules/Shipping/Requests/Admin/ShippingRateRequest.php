<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Requests\Admin;

use App\Core\Requests\BaseRequest;

class ShippingRateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [

            'shipping_zone_id' => [
                'required',
                'integer',
                'exists:shipping_zones,id',
            ],

            'shipping_method_id' => [
                'required',
                'integer',
                'exists:shipping_methods,id',
            ],

            'min_weight' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'max_weight' => [
                'nullable',
                'numeric',
                'gte:min_weight',
            ],

            'min_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'max_order_amount' => [
                'nullable',
                'numeric',
                'gte:min_order_amount',
            ],

            'base_rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'per_kg_rate' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'free_shipping_threshold' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }
}