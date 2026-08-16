<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

final class CreateSupplierUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:supplier_owner,supplier_purchase_manager,supplier_accounts,supplier_staff'],
            'is_primary_supplier_user' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
