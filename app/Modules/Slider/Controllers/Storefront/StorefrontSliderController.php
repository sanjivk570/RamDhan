<?php

declare(strict_types=1);

namespace App\Modules\Slider\Controllers\Storefront;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Slider\Actions\ShowStorefrontSliderAction;
use App\Modules\Slider\Resources\SliderResource;
use Illuminate\Http\JsonResponse;

/**
 * Storefront (public) slider controller.
 *
 * Exposes active sliders and their slide items to unauthenticated
 * storefront consumers. Only sliders that are active, with active
 * items inside their scheduling window, are returned.
 *
 * @package App\Modules\Slider\Controllers\Storefront
 * @author Sanjiv Kumar Kushwaha
 */
class StorefrontSliderController extends Controller
{
    /**
     * Create a new storefront slider controller instance.
     *
     * @param ShowStorefrontSliderAction $showAction
     */
    public function __construct(
        private readonly ShowStorefrontSliderAction $showAction
    ) {
    }

    /**
     * Display an active slider by its code.
     *
     * Example: GET /api/v1/storefront/sliders/home_hero
     *
     * @param string $code The slider code.
     * @return JsonResponse
     */
    public function show(string $code): JsonResponse
    {
        $slider = $this->showAction->execute($code);

        if (!$slider) {
            return ApiResponse::notFound('Slider not found.');
        }

        return ApiResponse::success(
            new SliderResource($slider),
            'Slider fetched successfully.'
        );
    }
}
