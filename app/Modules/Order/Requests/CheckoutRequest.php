<?php

declare(strict_types=1);

namespace App\Modules\Order\Requests;
use App\Core\Requests\BaseRequest;
final class CheckoutRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "guest_token" => ["nullable", "string", "max:120"],
            "email" => ["required", "email", "max:255"],
            "first_name" => ["required", "string", "max:100"],
            "last_name" => ["nullable", "string", "max:100"],
            "country_code" => ["nullable", "string", "max:10"],
            "phone" => ["nullable", "string", "max:40"],
            "shipping_address_uuid" => ["nullable", "uuid"],
            "billing_address_uuid" => ["nullable", "uuid"],
            "shipping_address" => ["nullable", "array"],
            "billing_address" => ["nullable", "array"],
            "payment_method" => ["required", "string", "max:40"],
            "customer_note" => ["nullable", "string", "max:2000"],
        ];
    }
}
