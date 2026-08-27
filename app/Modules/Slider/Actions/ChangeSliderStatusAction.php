<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider status change action.
 *
 * Delegates the process of changing a slider's active
 * status to the slider service.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ChangeSliderStatusAction
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
     * Execute the slider status change action.
     *
     * @param string $uuid The slider UUID.
     * @param bool $status The new status value.
     * @return Slider
     */
    public function execute(string $uuid, bool $status): Slider
    {
        return $this->sliderService->changeStatus($uuid, $status);
    }
}
