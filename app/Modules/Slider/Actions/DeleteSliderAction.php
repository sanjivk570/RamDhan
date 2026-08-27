<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider deletion action.
 *
 * Delegates the process of soft deleting a slider to the
 * slider service.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class DeleteSliderAction
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
     * Execute the slider deletion action.
     *
     * @param string $uuid The slider UUID.
     * @return void
     */
    public function execute(string $uuid): void
    {
        $this->sliderService->delete($uuid);
    }
}
