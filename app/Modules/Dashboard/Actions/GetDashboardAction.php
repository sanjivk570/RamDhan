<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Actions;

use App\Modules\Dashboard\Services\DashboardService;

final class GetDashboardAction
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    public function execute(int $days = 7): array
    {
        return $this->dashboardService
            ->getDashboardData($days);
    }
}