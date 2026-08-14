<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;

class UpdateCustomerProfileRequest extends BaseRequest
{
    public function rules(): array
    {
        $customer = $this->user();

        return [
            "first_name" => ["sometimes", "required", "string", "max:100"],

            "last_name" => ["nullable", "string", "max:100"],

            "country_code" => ["nullable", "string", "max:10"],

            "mobile" => [
                "nullable",
                "string",
                "max:30",
                "unique:customers,mobile," . ($customer?->id ?? "NULL"),
            ],
        ];
    }
}
