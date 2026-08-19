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
            "provider" => ["required", "string", "max:50"],
            "method" => ["required", "string", "max:40"],
        ];
    }
}
