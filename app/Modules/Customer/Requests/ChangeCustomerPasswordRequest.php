<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class ChangeCustomerPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "current_password" => ["required", "current_password:customer"],

            "new_password" => ["required", "confirmed", Password::defaults()],
        ];
    }
}
