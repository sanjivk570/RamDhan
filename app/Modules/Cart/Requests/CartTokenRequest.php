<?php

declare(strict_types=1);

namespace App\Modules\Cart\Requests;
use App\Core\Requests\BaseRequest;
final class CartTokenRequest extends BaseRequest
{
    public function rules(): array
    {
        return ["guest_token" => ["nullable", "string", "max:120"]];
    }
}
