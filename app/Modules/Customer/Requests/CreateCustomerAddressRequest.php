<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CreateCustomerAddressRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "type" => ["required", Rule::in(["shipping", "billing"])],

            "label" => ["nullable", "string", "max:50"],

            "first_name" => ["required", "string", "max:100"],

            "last_name" => ["nullable", "string", "max:100"],

            "company" => ["nullable", "string", "max:150"],

            "address_line_1" => ["required", "string", "max:255"],

            "address_line_2" => ["nullable", "string", "max:255"],

            "landmark" => ["nullable", "string", "max:255"],

            "city" => ["required", "string", "max:100"],

            "state" => ["required", "string", "max:100"],

            "state_code" => ["nullable", "string", "max:20"],

            "postal_code" => ["required", "string", "max:20"],

            "country" => ["required", "string", "max:100"],

            "country_code" => ["required", "string", "max:10"],

            "country_code_phone" => ["nullable", "string", "max:10"],

            "phone" => ["nullable", "string", "max:30"],

            "is_default" => ["sometimes", "boolean"],

            "is_active" => ["sometimes", "boolean"],
        ];
    }
}
