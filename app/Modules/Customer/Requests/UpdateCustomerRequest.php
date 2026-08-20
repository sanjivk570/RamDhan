<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends BaseRequest
{
    public function rules(): array
    {
        $uuid = $this->route("uuid");

        return [
            "first_name" => ["required", "string", "max:100"],

            "last_name" => ["nullable", "string", "max:100"],

            "email" => [
                "required",
                "email",
                "max:255",
                Rule::unique("customers", "email")->ignore($uuid, "uuid"),
            ],

            "country_code" => ["nullable", "string", "max:10"],

            "mobile" => [
                "nullable",
                "string",
                "max:30",
                Rule::unique("customers", "mobile")->ignore($uuid, "uuid"),
            ],

            "is_active" => ["sometimes", "boolean"],
        ];
    }
}
