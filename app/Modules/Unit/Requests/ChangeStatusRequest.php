<?php

declare(strict_types=1);

namespace App\Modules\Unit\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "status" => ["required", "boolean"],
        ];
    }
}
