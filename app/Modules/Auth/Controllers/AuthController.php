<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Core\BaseController;
use App\Modules\Auth\Actions\RegisterAction;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\UserResource;
use App\Core\Responses\ApiResponse;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Actions\LoginAction;
use App\Modules\Auth\Actions\LogoutAction;
use App\Modules\Auth\Actions\ProfileAction;
use App\Modules\Auth\Actions\ChangePasswordAction;
use App\Modules\Auth\Requests\ChangePasswordRequest;
use App\Modules\Auth\Actions\ForgotPasswordAction;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Actions\ResetPasswordAction;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;

/**
 * Handle authentication-related API requests.
 *
 * This controller manages user registration, authentication,
 * profile management, password updates, and password reset
 * operations by delegating business logic to dedicated actions.
 *
 * @package App\Modules\Auth\Controllers
 * @author Sanjiv Kumar Kushwaha
 */
class AuthController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @param RegisterAction $registerAction
     * @param LoginAction $loginAction
     * @param LogoutAction $logoutAction
     * @param ProfileAction $profileAction
     * @param ChangePasswordAction $changePasswordAction
     * @param ForgotPasswordAction $forgotPasswordAction
     * @param ResetPasswordAction $resetPasswordAction
     */
    public function __construct(
        protected RegisterAction $registerAction,
        protected LoginAction $loginAction,
        protected LogoutAction $logoutAction,
        protected ProfileAction $profileAction,
        protected ChangePasswordAction $changePasswordAction,
        protected ForgotPasswordAction $forgotPasswordAction,
        protected ResetPasswordAction $resetPasswordAction,
    ) {}

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $result = $this->registerAction->execute($request->validated());

        return ApiResponse::created([
            'user'  => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Registration successful.');
    }

    /**
     * Authenticate a user and return an access token.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $result = $this->loginAction->execute(
            $request->validated()
        );

        return ApiResponse::success([
            'user'  => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful.');
    }

    /**
     * Logout the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->logoutAction->execute($request->user());

        return ApiResponse::success(
            message: 'Logout successful.'
        );
    }

    /**
     * Retrieve the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $user = $this->profileAction->execute($request->user());
        return ApiResponse::success(
            new UserResource($user),
            'User profile fetched successfully.'
        );
    }

    /**
     * Change the authenticated user's password.
     *
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->changePasswordAction->execute(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success(
            null,
            'Password changed successfully.'
        );
    }

    /**
     * Send a password reset link to the user's email.
     *
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->forgotPasswordAction->execute(
            $request->validated()['email']
        );

        return ApiResponse::success(
            null,
            'Password reset link has been sent to your email.'
        );
    }

    /**
     * Reset the user's password using a valid reset token.
     *
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->resetPasswordAction->execute(
            $request->validated()
        );

        return ApiResponse::success(
            null,
            'Password reset successfully.'
        );
    }

}