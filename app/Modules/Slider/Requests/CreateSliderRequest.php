<?php

declare(strict_types=1);

namespace App\Modules\Slider\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Validate the request for creating a new slider.
 *
 * @package App\Modules\Slider\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class CreateSliderRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                'unique:sliders,code',
                'regex:/^[a-z0-9_]+$/',
            ],

            'placement' => [
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
