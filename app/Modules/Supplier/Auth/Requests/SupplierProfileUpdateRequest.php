<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Auth\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class SupplierProfileUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        $user = $this->user();
        $supplierId = $user?->supplier_id;

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'mobile' => ['nullable', 'string', 'max:30', Rule::unique('users', 'mobile')->ignore($user?->id)],
            'country_code' => ['nullable', 'string', 'max:10'],

            'company_name' => ['sometimes', 'string', 'max:200'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'supplier_email' => ['nullable', 'email', 'max:255', Rule::unique('suppliers', 'email')->ignore($supplierId)],
            'supplier_mobile' => ['nullable', 'string', 'max:30'],
            'alternate_mobile' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'gstin' => ['nullable', 'string', 'max:30'],
            'pan' => ['nullable', 'string', 'max:20'],
            'payment_terms_days' => ['sometimes', 'integer', 'min:0', 'max:3650'],
            'credit_limit' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
