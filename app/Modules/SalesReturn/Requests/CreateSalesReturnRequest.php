<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Requests;
use App\Core\Requests\BaseRequest;
final class CreateSalesReturnRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "order_uuid" => ["required", "uuid"],
            "reason" => ["nullable", "string", "max:500"],
            "customer_note" => ["nullable", "string", "max:2000"],
            "items" => ["required", "array", "min:1"],
            "items.*.order_item_uuid" => ["required", "uuid"],
            "items.*.quantity" => ["required", "numeric", "gt:0"],
            "items.*.reason" => ["nullable", "string", "max:500"],
        ];
    }
}
