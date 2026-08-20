<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Repositories;

use App\Modules\User\Models\User;
use Illuminate\Support\Collection;
use App\Modules\Role\Models\Permission;
use App\Modules\Role\Models\Role;

class DashboardRepository
{
    public function getOverview(): array
    {
        return [
            'total_users' => User::query()->count(),

            'active_users' => User::query()
                ->where('is_active', true)
                ->count(),

            'inactive_users' => User::query()
                ->where('is_active', false)
                ->count(),

            'total_roles' => Role::query()->count(),

            'total_permissions' => Permission::query()->count(),
        ];
    }

    public function getUserStatistics(int $days = 7): array
    {
        $fromDate = now()
            ->subDays($days - 1)
            ->startOfDay();

        return [
            'new_users' => User::query()
                ->where('created_at', '>=', $fromDate)
                ->count(),
        ];
    }

    public function getUserGrowth(int $days = 7): array
    {
        $fromDate = now()
            ->subDays($days - 1)
            ->startOfDay();

        $users = User::query()
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->where('created_at', '>=', $fromDate)
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $growth = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()
                ->subDays($i)
                ->format('Y-m-d');

            $growth[] = [
                'date' => $date,
                'total' => isset($users[$date])
                    ? (int) $users[$date]->total
                    : 0,
            ];
        }

        return $growth;
    }

    public function getRecentUsers(int $limit = 5): Collection
    {
        return User::query()
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}