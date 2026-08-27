<?php

declare(strict_types=1);

namespace App\Modules\Slider\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Validate the request for changing a slider status.
 *
 * @package App\Modules\Slider\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class ChangeSliderStatusRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'boolean',
            ],
        ];
    }
}
