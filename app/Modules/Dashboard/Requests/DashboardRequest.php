<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => [
                'nullable',
                'integer',
                'in:7,30,90',
            ],
        ];
    }

    public function days(): int
    {
        return (int) $this->input('days', 30);
    }
}