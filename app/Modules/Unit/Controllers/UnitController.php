<?php

declare(strict_types=1);

namespace App\Modules\Unit\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Unit\Actions\ChangeStatusAction;
use App\Modules\Unit\Actions\CreateUnitAction;
use App\Modules\Unit\Actions\DeleteUnitAction;
use App\Modules\Unit\Actions\ListUnitAction;
use App\Modules\Unit\Actions\RestoreUnitAction;
use App\Modules\Unit\Actions\ShowUnitAction;
use App\Modules\Unit\Actions\UpdateUnitAction;
use App\Modules\Unit\Requests\ChangeStatusRequest;
use App\Modules\Unit\Requests\CreateUnitRequest;
use App\Modules\Unit\Requests\UnitListRequest;
use App\Modules\Unit\Requests\UpdateUnitRequest;
use App\Modules\Unit\Resources\UnitResource;

class UnitController extends Controller
{
    public function __construct(
        private readonly ListUnitAction $listUnitAction,
        private readonly ShowUnitAction $showUnitAction,
        private readonly CreateUnitAction $createUnitAction,
        private readonly UpdateUnitAction $updateUnitAction,
        private readonly DeleteUnitAction $deleteUnitAction,
        private readonly RestoreUnitAction $restoreUnitAction,
        private readonly ChangeStatusAction $changeStatusAction
    ) {
    }

    /**
     * GET /units
     */
    public function index(UnitListRequest $request)
    {
        $units = $this->listUnitAction->execute($request->validated());

        return ApiResponse::paginated(
            $units,
            UnitResource::collection($units),
            "Units fetched successfully."
        );
    }

    /**
     * GET /units/{uuid}
     */
    public function show(string $uuid)
    {
        $unit = $this->showUnitAction->execute($uuid);

        return ApiResponse::success(
            new UnitResource($unit),
            "Unit fetched successfully."
        );
    }

    /**
     * POST /units
     */
    public function store(CreateUnitRequest $request)
    {
        $unit = $this->createUnitAction->execute($request->validated());

        return ApiResponse::success(
            new UnitResource($unit),
            "Unit created successfully."
        );
    }

    /**
     * PUT /units/{uuid}
     */
    public function update(UpdateUnitRequest $request, string $uuid)
    {
        $unit = $this->updateUnitAction->execute($uuid, $request->validated());

        return ApiResponse::success(
            new UnitResource($unit),
            "Unit updated successfully."
        );
    }

    /**
     * PATCH /units/{uuid}/status
     */
    public function changeStatus(ChangeStatusRequest $request, string $uuid)
    {
        $unit = $this->changeStatusAction->execute(
            $uuid,
            $request->boolean("status")
        );

        return ApiResponse::success(
            new UnitResource($unit),
            "Unit status updated successfully."
        );
    }

    /**
     * DELETE /units/{uuid}
     */
    public function destroy(string $uuid)
    {
        $this->deleteUnitAction->execute($uuid);

        return ApiResponse::success([], "Unit deleted successfully.");
    }

    /**
     * POST /units/{uuid}/restore
     */
    public function restore(string $uuid)
    {
        $unit = $this->restoreUnitAction->execute($uuid);

        return ApiResponse::success(
            new UnitResource($unit),
            "Unit restored successfully."
        );
    }
}
