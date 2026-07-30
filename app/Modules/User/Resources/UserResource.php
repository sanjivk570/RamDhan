<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform user data into a JSON API resource.
 *
 * Responsible for controlling the structure of user
 * data returned through API responses.
 *
 * @package App\Modules\User\Resources
 * @author Sanjiv Kumar Kushwaha
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Converts the user model data into a formatted
     * API response representation.
     *
     * @param Request $request The incoming HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'full_name'  => $this->full_name,
            'email'      => $this->email,
            'mobile'     => $this->mobile,
            'status'     => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}