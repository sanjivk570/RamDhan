<?php

declare(strict_types=1);

namespace App\Modules\Cart\Requests;
use App\Core\Requests\BaseRequest;
final class UpdateCartItemRequest extends BaseRequest
{
    public function rules(): array
    {
        return ["quantity" => ["required", "numeric", "gt:0", "max:9999"]];
    }
}
