<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Controllers;

use App\Core\Responses\ApiResponse;
use App\Modules\Dashboard\Actions\GetDashboardAction;
use App\Modules\Dashboard\Requests\DashboardRequest;
use App\Modules\Dashboard\Resources\DashboardResource;
use Illuminate\Http\JsonResponse;

final class DashboardController
{
    public function __construct(
        private readonly GetDashboardAction $getDashboardAction
    ) {
    }

    public function index(
        DashboardRequest $request
    ): JsonResponse {
        $dashboard = $this->getDashboardAction->execute(
            $request->days()
        );

        return ApiResponse::success(
            data: new DashboardResource($dashboard),
            message: 'Dashboard data fetched successfully.'
        );
    }
}