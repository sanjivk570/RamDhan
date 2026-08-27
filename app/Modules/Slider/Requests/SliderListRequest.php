<?php

declare(strict_types=1);

namespace App\Modules\Slider\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Validate the request for listing sliders.
 *
 * @package App\Modules\Slider\Requests
 * @author Sanjiv Kumar Kushwaha
 */
class SliderListRequest extends BaseRequest
{
    private const SORTABLE_COLUMNS = [
        'name',
        'code',
        'placement',
        'is_active',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],

            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],

            'sort_by' => [
                'nullable',
                'in:' . implode(',', self::SORTABLE_COLUMNS),
            ],

            'sort_order' => [
                'nullable',
                'in:asc,desc',
            ],

            'filters' => [
                'nullable',
                'array',
            ],

            'filters.name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'filters.code' => [
                'nullable',
                'string',
                'max:100',
            ],

            'filters.placement' => [
                'nullable',
                'string',
                'max:100',
            ],

            'filters.status' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
