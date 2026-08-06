<?php

declare(strict_types=1);

namespace App\Modules\Role\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform a role model into an API resource.
 *
 * Formats role data for JSON responses.
 *
 * @package App\Modules\Role\Resources
 * @author Sanjiv Kumar Kushwaha
 */
class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
            'is_system' => $this->is_system,
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'display_name' => $permission->display_name,
                        'module' => $permission->module,
                    ];
                });
            }),
            'created_at' => $this->created_at,
        ];
    }
}