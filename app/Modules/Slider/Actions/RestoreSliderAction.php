<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider restore action.
 *
 * Delegates the process of restoring a soft-deleted slider
 * to the slider service.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class RestoreSliderAction
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
     * Execute the slider restore action.
     *
     * @param string $uuid The slider UUID.
     * @return \App\Modules\Slider\Models\Slider
     */
    public function execute(string $uuid)
    {
        return $this->sliderService->restore($uuid);
    }
}
