<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SupplierUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $this->roles->first();

        return [
            'uuid' => $this->uuid,
            'supplier_id' => $this->supplier?->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'mobile' => $this->mobile,
            'avatar' => $this->avatar,
            'user_type' => $this->user_type,
            'is_primary_supplier_user' => $this->is_primary_supplier_user,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at,
            'role' => $role ? [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name ?? $role->name,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
