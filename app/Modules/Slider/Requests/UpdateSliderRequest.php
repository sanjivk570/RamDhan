<?php

declare(strict_types=1);

namespace App\Modules\Slider\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for updating a slider.
 *
 * @package App\Modules\Slider\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateSliderRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $sliderUuid = $this->route('uuid');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('sliders', 'code')->ignore($sliderUuid, 'uuid'),
            ],

            'placement' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
