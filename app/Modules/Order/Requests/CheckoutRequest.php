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
            "shipping_address.street" => ["required_with:shipping_address", "string", "max:255"],
            "shipping_address.city" => ["required_with:shipping_address", "string", "max:100"],
            "shipping_address.state" => ["required_with:shipping_address", "string", "max:100"],
            "shipping_address.postal_code" => ["required_with:shipping_address", "string", "max:20"],
            "shipping_address.country" => ["required_with:shipping_address", "string", "max:2"],
            "billing_address" => ["nullable", "array"],
            "billing_address.street" => ["required_with:billing_address", "string", "max:255"],
            "billing_address.city" => ["required_with:billing_address", "string", "max:100"],
            "billing_address.state" => ["required_with:billing_address", "string", "max:100"],
            "billing_address.postal_code" => ["required_with:billing_address", "string", "max:20"],
            "billing_address.country" => ["required_with:billing_address", "string", "max:2"],
            "payment_method" => ["required", "string", "in:cod,stripe,paypal,razorpay,wallet"],
            "customer_note" => ["nullable", "string", "max:2000"],
            // Optional shipping selection; when omitted the rate already
            // applied to the cart (if any) is validated and reused.
            "shipping_rate_uuid" => ["nullable", "uuid"],
        ];
    }
}
