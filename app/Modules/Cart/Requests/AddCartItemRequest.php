<?php

declare(strict_types=1);

namespace App\Modules\Cart\Requests;
use App\Core\Requests\BaseRequest;
final class AddCartItemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "product_uuid" => ["required", "uuid"],
            "variant_uuid" => ["nullable", "uuid"],
            "quantity" => ["required", "numeric", "gt:0", "max:9999"],
        ];
    }
}
