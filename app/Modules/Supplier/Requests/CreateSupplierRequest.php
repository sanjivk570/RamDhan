<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Requests;

use App\Core\Requests\BaseRequest;

class CreateSupplierRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "supplier_code" => [
                "nullable",
                "string",
                "max:50",
                "unique:suppliers,supplier_code",
            ],

            "company_name" => ["required", "string", "max:200"],

            "contact_person" => ["nullable", "string", "max:150"],

            "email" => ["nullable", "email", "max:255"],

            "country_code" => ["nullable", "string", "max:10"],

            "mobile" => ["nullable", "string", "max:30"],

            "alternate_mobile" => ["nullable", "string", "max:30"],

            "website" => ["nullable", "url", "max:255"],

            "gstin" => ["nullable", "string", "max:30"],

            "pan" => ["nullable", "string", "max:20"],

            "payment_terms_days" => [
                "nullable",
                "integer",
                "min:0",
                "max:3650",
            ],

            "credit_limit" => [
                "nullable",
                "numeric",
                "min:0",
                "max:999999999999.99",
            ],

            "notes" => ["nullable", "string", "max:5000"],

            "is_active" => ["sometimes", "boolean"],
        ];
    }
}
