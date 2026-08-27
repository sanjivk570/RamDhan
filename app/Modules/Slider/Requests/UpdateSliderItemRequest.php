<?php

declare(strict_types=1);

namespace App\Modules\Slider\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Validate the request for updating a slider item.
 *
 * @package App\Modules\Slider\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class UpdateSliderItemRequest extends BaseRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:191',
            ],

            'subtitle' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'button_text' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'button_url' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'starts_at' => [
                'sometimes',
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
            ],

            'ends_at' => [
                'sometimes',
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:starts_at',
            ],
        ];
    }
}
