<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Auth\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

class SupplierChangePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
