<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider creation action.
 *
 * Delegates the process of creating a new slider to the
 * slider service.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
final class CreateSliderAction
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
     * Execute the slider creation action.
     *
     * @param array<string, mixed> $data The validated slider data.
     * @return Slider
     */
    public function execute(array $data): Slider
    {
        return $this->sliderService->create($data);
    }
}
