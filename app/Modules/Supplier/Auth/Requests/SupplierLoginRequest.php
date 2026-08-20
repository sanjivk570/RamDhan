<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Auth\Requests;

use App\Core\Requests\BaseRequest;

class SupplierLoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }
}
