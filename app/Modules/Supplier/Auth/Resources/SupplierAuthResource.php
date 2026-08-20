<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierAuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $supplier = $this->supplier;
        $role = $this->roles->first();

        return [
            'user' => [
                'uuid' => $this->uuid,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'full_name' => $this->full_name,
                'email' => $this->email,
                'country_code' => $this->country_code,
                'mobile' => $this->mobile,
                'is_active' => $this->is_active,
                'last_login_at' => $this->last_login_at,
            ],
            'supplier' => $supplier ? [
                'uuid' => $supplier->uuid,
                'supplier_code' => $supplier->supplier_code,
                'company_name' => $supplier->company_name,
                'contact_person' => $supplier->contact_person,
                'email' => $supplier->email,
                'mobile' => $supplier->mobile,
                'gstin' => $supplier->gstin,
                'pan' => $supplier->pan,
                'payment_terms_days' => $supplier->payment_terms_days,
                'credit_limit' => $supplier->credit_limit,
                'is_active' => $supplier->is_active,
            ] : null,
            'role' => $role ? [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name ?? $role->name,
            ] : null,
        ];
    }
}
