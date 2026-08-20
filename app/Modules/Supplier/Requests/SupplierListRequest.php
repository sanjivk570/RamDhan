<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Requests;

use App\Core\Requests\BaseRequest;

class SupplierListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        "supplier_code",
        "company_name",
        "contact_person",
        "email",
        "mobile",
        "gstin",
        "pan",
        "payment_terms_days",
        "credit_limit",
        "created_at",
        "updated_at",
    ];

    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:255"],

            "per_page" => ["nullable", "integer", "min:10", "max:100"],

            "sort_by" => [
                "nullable",
                "in:" . implode(",", self::SORTABLE_COLUMNS),
            ],

            "sort_order" => ["nullable", "in:asc,desc"],

            "filters" => ["nullable", "array"],

            "filters.supplier_code" => ["nullable", "string", "max:50"],

            "filters.company_name" => ["nullable", "string", "max:200"],

            "filters.contact_person" => ["nullable", "string", "max:150"],

            "filters.email" => ["nullable", "string", "max:255"],

            "filters.mobile" => ["nullable", "string", "max:30"],

            "filters.gstin" => ["nullable", "string", "max:30"],

            "filters.pan" => ["nullable", "string", "max:20"],

            "filters.status" => ["nullable", "boolean"],
        ];
    }
}
