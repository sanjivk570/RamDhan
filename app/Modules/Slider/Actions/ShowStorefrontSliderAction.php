<?php

declare(strict_types=1);

namespace App\Modules\Slider\Actions;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Services\SliderService;

/**
 * Show an active slider from the public storefront.
 *
 * @package App\Modules\Slider\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ShowStorefrontSliderAction
{
    /**
     * Create a new show storefront slider action.
     *
     * @param SliderService $service
     */
    public function __construct(
        private readonly SliderService $service
    ) {
    }

    /**
     * Execute storefront slider retrieval.
     *
     * @param string $code The slider code.
     * @return Slider|null
     */
    public function execute(string $code): ?Slider
    {
        return $this->service->storefrontDetails($code);
    }
}
