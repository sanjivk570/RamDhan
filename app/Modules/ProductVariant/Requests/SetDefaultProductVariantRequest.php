<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetDefaultProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
