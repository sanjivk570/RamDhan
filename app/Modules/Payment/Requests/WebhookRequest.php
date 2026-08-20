<?php

declare(strict_types=1);

namespace App\Modules\Payment\Requests;
use App\Core\Requests\BaseRequest;
final class WebhookRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "provider" => ["required", "string", "max:50"],
            "event" => ["required", "string", "max:100"],
            "payload" => ["required", "array"],
        ];
    }
}
