<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends BaseRequest
{
    public function rules(): array
    {
        $uuid = $this->route("uuid");

        return [
            "supplier_code" => [
                "sometimes",
                "nullable",
                "string",
                "max:50",
                Rule::unique("suppliers", "supplier_code")->ignore(
                    $uuid,
                    "uuid"
                ),
            ],

            "company_name" => ["sometimes", "required", "string", "max:200"],

            "contact_person" => ["sometimes", "nullable", "string", "max:150"],

            "email" => ["sometimes", "nullable", "email", "max:255"],

            "country_code" => ["sometimes", "nullable", "string", "max:10"],

            "mobile" => ["sometimes", "nullable", "string", "max:30"],

            "alternate_mobile" => ["sometimes", "nullable", "string", "max:30"],

            "website" => ["sometimes", "nullable", "url", "max:255"],

            "gstin" => ["sometimes", "nullable", "string", "max:30"],

            "pan" => ["sometimes", "nullable", "string", "max:20"],

            "payment_terms_days" => [
                "sometimes",
                "nullable",
                "integer",
                "min:0",
                "max:3650",
            ],

            "credit_limit" => [
                "sometimes",
                "nullable",
                "numeric",
                "min:0",
                "max:999999999999.99",
            ],

            "notes" => ["sometimes", "nullable", "string", "max:5000"],

            "is_active" => ["sometimes", "boolean"],
        ];
    }
}
