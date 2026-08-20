<?php

declare(strict_types=1);

namespace App\Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "file" => [
                "required",
                "file",
                "max:10240",
                "mimes:jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,xls,xlsx,mp4,mov",
            ],

            "mediable_type" => [
                "required",
                Rule::in(["product", "category", "brand", "user"]),
            ],

            'collection' => ['nullable', 'string', 'max:100'],

            "mediable_uuid" => ["required", "uuid"],

            "title" => ["nullable", "string", "max:255"],

            "alt_text" => ["nullable", "string", "max:255"],

            "description" => ["nullable", "string"],

            "sort_order" => ["nullable", "integer", "min:0"],

            "is_primary" => ["nullable", "boolean"],
        ];
    }
}
