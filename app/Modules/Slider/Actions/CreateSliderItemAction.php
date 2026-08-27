<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Models\SliderItem;
use App\Modules\Slider\Services\SliderService;

/**
 * Handle the slider item creation action.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class CreateSliderItemAction
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
     * Execute the slider item creation action.
     *
     * @param string $sliderUuid The parent slider UUID.
     * @param array<string, mixed> $data The validated slider item data.
     * @return SliderItem
     */
    public function execute(string $sliderUuid, array $data): SliderItem
    {
        return $this->sliderService->createItem($sliderUuid, $data);
    }
}
