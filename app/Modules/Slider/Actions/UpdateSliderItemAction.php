<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Models\SliderItem;
use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider item update action.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateSliderItemAction
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
     * Execute the slider item update action.
     *
     * @param string $sliderUuid The parent slider UUID.
     * @param string $itemUuid The slider item UUID.
     * @param array<string, mixed> $data The validated data.
     * @return SliderItem
     */
    public function execute(string $sliderUuid, string $itemUuid, array $data): SliderItem
    {
        return $this->sliderService->updateItem($sliderUuid, $itemUuid, $data);
    }
}
