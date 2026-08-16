<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;

final class UpdateSupplierUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'email:rfc', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['sometimes', 'string', 'in:supplier_owner,supplier_purchase_manager,supplier_accounts,supplier_staff'],
            'is_primary_supplier_user' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
