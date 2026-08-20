<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

/**
 * Handle authentication-related business logic.
 *
 * This service is responsible for user registration, authentication,
 * logout, password management, and password reset operations.
 *
 * @package App\Modules\Auth\Services
 * @author Sanjiv Kumar Kushwaha
 */
class AuthService
{
    /**
     * Register a new user and generate an API token.
     *
     * @param array<string, mixed> $data The validated registration data.
     * @return array<string, mixed>
     */
    public function register(array $data)
    {
        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate a user and generate an API token.
     *
     * @param array<string, mixed> $credentials The user login credentials.
     * @return array<string, mixed>
     *
     * @throws UnauthorizedHttpException
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new UnauthorizedHttpException('', 'Invalid credentials.');
        }

        // Optional: delete old tokens
        // $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Revoke the current access token for the authenticated user.
     *
     * @param User $user The authenticated user.
     * @return void
     */
    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }
    }

    /**
     * Change the authenticated user's password.
     *
     * @param User $user The authenticated user.
     * @param array<string, mixed> $data The validated password data.
     * @return void
     *
     * @throws UnauthorizedHttpException
     */
    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new UnauthorizedHttpException('', 'Current password is incorrect.');
        }

        $user->update([
            'password' => $data['new_password'], // hashed cast automatically
        ]);

        // Optional: Logout from all devices
        // $user->tokens()->delete();
    }

    /**
     * Send a password reset link to the user's email address.
     *
     * @param string $email The user's email address.
     * @return void
     *
     * @throws HttpException
     */
    public function forgotPassword(string $email): void
    {
        $status = Password::sendResetLink([
            'email' => $email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new HttpException(
                422,
                __($status)
            );
        }

        // ResetPassword::createUrlUsing(function ($user, string $token) {
        //     return config('app.frontend_url') . '/reset-password?token='. $token . '&email=' . urlencode($user->email);

        // });

    }

    /**
     * Reset the user's password using a valid reset token.
     *
     * @param array<string, mixed> $data The validated password reset data.
     * @return void
     *
     * @throws HttpException
     */
    public function resetPassword(array $data): void
    {
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                //'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function (User $user, string $password): void {

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new HttpException(422, __($status));
        }
    }

}