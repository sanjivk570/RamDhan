<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider update action.
 *
 * Delegates the process of updating an existing slider to the
 * slider service.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateSliderAction
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
     * Execute the slider update action.
     *
     * @param string $uuid The slider UUID.
     * @param array<string, mixed> $data The validated slider data.
     * @return Slider
     */
    public function execute(string $uuid, array $data): Slider
    {
        return $this->sliderService->update($uuid, $data);
    }
}
