<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider retrieval action.
 *
 * Delegates the process of retrieving the details of a specific
 * slider to the slider service.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class ShowSliderAction
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
     * Execute the slider retrieval action.
     *
     * @param string $uuid The slider UUID.
     * @return Slider
     */
    public function execute(string $uuid): Slider
    {
        return $this->sliderService->details($uuid);
    }
}
