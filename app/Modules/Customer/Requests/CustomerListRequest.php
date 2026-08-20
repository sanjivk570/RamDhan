<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;

class CustomerListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        "first_name",
        "last_name",
        "email",
        "mobile",
        "customer_code",
        "created_at",
        "updated_at",
    ];

    public function rules(): array
    {
        return [
            "search" => ["nullable", "string"],

            "per_page" => ["nullable", "integer", "min:10", "max:100"],

            "sort_by" => [
                "nullable",
                "in:" . implode(",", self::SORTABLE_COLUMNS),
            ],

            "sort_order" => ["nullable", "in:asc,desc"],

            "filters" => ["nullable", "array"],

            "filters.first_name" => ["nullable", "string", "max:100"],

            "filters.last_name" => ["nullable", "string", "max:100"],

            "filters.email" => ["nullable", "string", "max:255"],

            "filters.mobile" => ["nullable", "string", "max:30"],

            "filters.status" => ["nullable", "boolean"],
        ];
    }
}
