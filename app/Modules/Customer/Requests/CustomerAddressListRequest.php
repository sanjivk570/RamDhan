<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CustomerAddressListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "type" => ["nullable", Rule::in(["shipping", "billing"])],

            "is_active" => ["nullable", "boolean"],

            "per_page" => ["nullable", "integer", "min:10", "max:100"],
        ];
    }
}
