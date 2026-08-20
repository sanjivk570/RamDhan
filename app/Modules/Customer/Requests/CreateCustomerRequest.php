<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class CreateCustomerRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:100"],

            "last_name" => ["nullable", "string", "max:100"],

            "email" => [
                "required",
                "email",
                "max:255",
                "unique:customers,email",
            ],

            "country_code" => ["nullable", "string", "max:10"],

            "mobile" => [
                "nullable",
                "string",
                "max:30",
                "unique:customers,mobile",
            ],

            "password" => ["required", "confirmed", Password::defaults()],

            "is_active" => ["sometimes", "boolean"],
        ];
    }
}
