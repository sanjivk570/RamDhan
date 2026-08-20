<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use App\Core\Requests\BaseRequest;

/**
 * Handle validation rules for forgot password requests.
 *
 * This request validates the email address required to initiate
 * the password reset process.
 *
 * @package App\Modules\Auth\Requests
 * @author Sanjiv Kumar Kushwaha
 */
final class ForgotPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules for the forgot password request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}