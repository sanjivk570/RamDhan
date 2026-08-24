<?php

declare(strict_types=1);

namespace App\Modules\Cart\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Validate the "apply shipping method to cart" request.
 *
 * The shipping amount is never accepted from the frontend; only the
 * selected rate UUID plus an optional destination override.
 *
 * @package App\Modules\Cart\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class ApplyShippingRequest extends BaseRequest
{
    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "shipping_rate_uuid" => ["required", "uuid"],

            // Optional destination override (otherwise the destination
            // captured during the shipping-rates lookup is reused).
            "customer_address_uuid" => ["nullable", "uuid"],
            "country_code" => ["nullable", "string", "max:2"],
            "country" => ["nullable", "string", "max:2"],
            "state_code" => ["nullable", "string", "max:10"],
            "state" => ["nullable", "string", "max:10"],
            "postal_code" => ["nullable", "string", "max:20"],
        ];
    }
}