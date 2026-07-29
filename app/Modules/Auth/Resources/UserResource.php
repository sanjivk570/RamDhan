<?php

namespace App\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transform the authenticated user model into a JSON resource.
 *
 * This resource defines the user data returned in API responses.
 *
 * @package App\Modules\Auth\Resources
 * @author Sanjiv Kumar Kushwaha
 */
class UserResource extends JsonResource
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
            'uuid'       => $this->uuid,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'full_name'  => $this->full_name,
            'email'      => $this->email,
            'mobile'     => $this->mobile,
        ];
    }
}