<?php

declare(strict_types=1);

namespace App\Modules\Slider\Requests;

use App\Core\Requests\BaseRequest;
use Illuminate\Validation\Rule;

/**
 * Validate the request for creating a new slider item.
 *
 * @package App\Modules\Slider\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class CreateSliderItemRequest extends BaseRequest
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
                'required',
                'string',
                'max:191',
            ],

            'subtitle' => [
                'nullable',
                'string',
                'max:255',
            ],

            'button_text' => [
                'nullable',
                'string',
                'max:100',
            ],

            'button_url' => [
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
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:starts_at',
            ],
        ];
    }
}
