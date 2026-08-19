<?php

declare(strict_types=1);

namespace App\Modules\Order\Requests;
use App\Core\Requests\BaseRequest;
final class OrderListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:100"],
            "status" => ["nullable", "string", "max:30"],
            "payment_status" => ["nullable", "string", "max:30"],
            "fulfillment_status" => ["nullable", "string", "max:30"],
            "customer_id" => ["nullable", "integer"],
            "from_date" => ["nullable", "date"],
            "to_date" => ["nullable", "date"],
            "per_page" => ["nullable", "integer", "min:10", "max:100"],
            "sort_by" => [
                "nullable",
                "in:created_at,grand_total,status,order_number",
            ],
            "sort_order" => ["nullable", "in:asc,desc"],
        ];
    }
}
