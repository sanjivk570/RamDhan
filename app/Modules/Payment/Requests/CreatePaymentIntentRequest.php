<?php

declare(strict_types=1);

namespace App\Modules\Payment\Requests;
use App\Core\Requests\BaseRequest;
final class CreatePaymentIntentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "order_uuid" => ["required", "uuid"],
            "provider" => ["required", "string", "in:stripe,razorpay,paypal,wallet"],
            "method" => ["required", "string", "in:card,upi,netbanking,wallet,cod"],
        ];
    }
}
