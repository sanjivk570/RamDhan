<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'overview' => $this->resource['overview'],

            'user_statistics' => $this->resource[
                'user_statistics'
            ],

            'user_growth' => $this->resource[
                'user_growth'
            ],

            'recent_users' => $this->resource[
                'recent_users'
            ]->map(function ($user): array {
                return [
                    'uuid' => $user->uuid,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'status' => (bool) $user->is_active,
                    'created_at' => $user->created_at,
                ];
            })->values(),
        ];
    }
}