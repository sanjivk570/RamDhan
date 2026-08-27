<?php

declare(strict_types=1);

namespace App\Modules\Slider\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Slider\Actions\ChangeSliderStatusAction;
use App\Modules\Slider\Actions\CreateSliderAction;
use App\Modules\Slider\Actions\CreateSliderItemAction;
use App\Modules\Slider\Actions\DeleteSliderAction;
use App\Modules\Slider\Actions\DeleteSliderItemAction;
use App\Modules\Slider\Actions\ListSliderAction;
use App\Modules\Slider\Actions\RestoreSliderAction;
use App\Modules\Slider\Actions\ShowSliderAction;
use App\Modules\Slider\Actions\UpdateSliderAction;
use App\Modules\Slider\Actions\UpdateSliderItemAction;
use App\Modules\Slider\Requests\ChangeSliderStatusRequest;
use App\Modules\Slider\Requests\CreateSliderItemRequest;
use App\Modules\Slider\Requests\CreateSliderRequest;
use App\Modules\Slider\Requests\SliderListRequest;
use App\Modules\Slider\Requests\UpdateSliderItemRequest;
use App\Modules\Slider\Requests\UpdateSliderRequest;
use App\Modules\Slider\Resources\SliderItemResource;
use App\Modules\Slider\Resources\SliderResource;
use Illuminate\Http\JsonResponse;

/**
 * Controller responsible for slider management operations.
 *
 * Handles slider listing, retrieval, creation, updating,
 * status changes, deletion, and restoration as well as
 * the management of their slide items.
 *
 * @package App\Modules\Slider\Controllers
 * @author Sanjiv Kumar Kushwaha
 */
class SliderController extends Controller
{
    public function __construct(
        private readonly ListSliderAction $listSliderAction,
        private readonly ShowSliderAction $showSliderAction,
        private readonly CreateSliderAction $createSliderAction,
        private readonly UpdateSliderAction $updateSliderAction,
        private readonly DeleteSliderAction $deleteSliderAction,
        private readonly RestoreSliderAction $restoreSliderAction,
        private readonly ChangeSliderStatusAction $changeSliderStatusAction,
        private readonly CreateSliderItemAction $createSliderItemAction,
        private readonly UpdateSliderItemAction $updateSliderItemAction,
        private readonly DeleteSliderItemAction $deleteSliderItemAction
    ) {
    }

    /**
     * Display a paginated listing of sliders.
     */
    public function index(SliderListRequest $request): JsonResponse
    {
        $sliders = $this->listSliderAction->execute(
            $request->validated()
        );

        return ApiResponse::paginated(
            $sliders,
            SliderResource::collection($sliders),
            'Sliders fetched successfully.'
        );
    }

    /**
     * Display the specified slider.
     */
    public function show(string $uuid): JsonResponse
    {
        $slider = $this->showSliderAction->execute($uuid);

        return ApiResponse::success(
            new SliderResource($slider),
            'Slider fetched successfully.'
        );
    }

    /**
     * Store a newly created slider.
     */
    public function store(CreateSliderRequest $request): JsonResponse
    {
        $slider = $this->createSliderAction->execute(
            $request->validated()
        );

        return ApiResponse::created(
            new SliderResource($slider),
            'Slider created successfully.'
        );
    }

    /**
     * Update the specified slider.
     */
    public function update(
        UpdateSliderRequest $request,
        string $uuid
    ): JsonResponse {
        $slider = $this->updateSliderAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::updated(
            new SliderResource($slider),
            'Slider updated successfully.'
        );
    }

    /**
     * Change the status of the specified slider.
     */
    public function changeStatus(
        ChangeSliderStatusRequest $request,
        string $uuid
    ): JsonResponse {
        $slider = $this->changeSliderStatusAction->execute(
            $uuid,
            $request->boolean('status')
        );

        return ApiResponse::updated(
            new SliderResource($slider),
            'Slider status updated successfully.'
        );
    }

    /**
     * Soft delete the specified slider.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $this->deleteSliderAction->execute($uuid);

        return ApiResponse::deleted('Slider deleted successfully.');
    }

    /**
     * Restore a soft-deleted slider.
     */
    public function restore(string $uuid): JsonResponse
    {
        $slider = $this->restoreSliderAction->execute($uuid);

        return ApiResponse::success(
            new SliderResource($slider),
            'Slider restored successfully.'
        );
    }

    /**
     * Store a new slider item (slide).
     */
    public function storeItem(
        CreateSliderItemRequest $request,
        string $uuid
    ): JsonResponse {
        $sliderItem = $this->createSliderItemAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::created(
            new SliderItemResource($sliderItem),
            'Slider item created successfully.'
        );
    }

    /**
     * Update an existing slider item (slide).
     */
    public function updateItem(
        UpdateSliderItemRequest $request,
        string $uuid,
        string $itemUuid
    ): JsonResponse {
        $sliderItem = $this->updateSliderItemAction->execute(
            $uuid,
            $itemUuid,
            $request->validated()
        );

        return ApiResponse::updated(
            new SliderItemResource($sliderItem),
            'Slider item updated successfully.'
        );
    }

    /**
     * Delete a slider item (slide).
     */
    public function destroyItem(
        string $uuid,
        string $itemUuid
    ): JsonResponse {
        $this->deleteSliderItemAction->execute($uuid, $itemUuid);

        return ApiResponse::deleted('Slider item deleted successfully.');
    }
}

