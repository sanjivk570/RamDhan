<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate product status change requests.
 *
 * @author Sanjiv Kumar Kushwaha
 */
class ChangeStatusRequest extends FormRequest
{

    /**
     * Determine whether the user is authorized
     * to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "status" => ["required", "boolean"],
        ];
    }
}
