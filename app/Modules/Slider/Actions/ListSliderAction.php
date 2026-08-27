<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider listing action.
 *
 * Delegates the process of retrieving sliders based on
 * the provided filters to the slider service.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ListSliderAction
{
    /**
     * Create a new action instance.
     *
     * @param SliderService $sliderService The slider service.
     */
    public function __construct(
        private readonly SliderService $sliderService
    ) {
    }

    /**
     * Execute the slider listing action.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        return $this->sliderService->list($filters);
    }
}
