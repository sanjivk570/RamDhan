<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Services;

use App\Modules\Dashboard\Repositories\DashboardRepository;

final class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $dashboardRepository
    ) {
    }

    public function getDashboardData(int $days = 7): array
    {
        return [
            'overview' => $this->dashboardRepository
                ->getOverview(),

            'user_statistics' => $this->dashboardRepository
                ->getUserStatistics($days),

            'user_growth' => $this->dashboardRepository
                ->getUserGrowth($days),

            'recent_users' => $this->dashboardRepository
                ->getRecentUsers(5),
        ];
    }
}