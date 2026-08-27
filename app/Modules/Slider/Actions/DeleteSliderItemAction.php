<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider item deletion action.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class DeleteSliderItemAction
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
     * Execute the slider item deletion action.
     *
     * @param string $sliderUuid The parent slider UUID.
     * @param string $itemUuid The slider item UUID.
     * @return void
     */
    public function execute(string $sliderUuid, string $itemUuid): void
    {
        $this->sliderService->deleteItem($sliderUuid, $itemUuid);
    }
}
