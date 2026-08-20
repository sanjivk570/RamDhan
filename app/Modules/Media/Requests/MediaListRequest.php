<?php

declare(strict_types=1);

namespace App\Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:255"],

            "filters" => ["nullable", "array"],

            'filters.collection' => ['nullable', 'string', 'max:100'],

            "filters.original_name" => ["nullable", "string", "max:255"],

            "filters.title" => ["nullable", "string", "max:255"],

            "filters.type" => [
                "nullable",
                Rule::in(["image", "video", "document", "other"]),
            ],

            "filters.mime_type" => ["nullable", "string", "max:255"],

            "filters.is_primary" => ["nullable", "boolean"],

            "filters.mediable_type" => ["nullable", "string", "max:255"],

            "sort_by" => [
                "nullable",
                Rule::in([
                    "original_name",
                    "file_name",
                    "collection",
                    "size",
                    "sort_order",
                    "created_at",
                    "updated_at",
                ]),
            ],

            "sort_order" => ["nullable", Rule::in(["asc", "desc"])],

            "per_page" => ["nullable", "integer", "min:1", "max:100"],

            "page" => ["nullable", "integer", "min:1"],
        ];
    }
}
