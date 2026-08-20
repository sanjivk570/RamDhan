<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Auth\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class SupplierResetPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
