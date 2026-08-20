<?php

declare(strict_types=1);

namespace App\Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title" => ["sometimes", "nullable", "string", "max:255"],

            "alt_text" => ["sometimes", "nullable", "string", "max:255"],

            "description" => ["sometimes", "nullable", "string"],

            "sort_order" => ["sometimes", "integer", "min:0"],

            "is_primary" => ["sometimes", "boolean"],
        ];
    }
}
