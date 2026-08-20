<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Auth\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Supplier\Auth\Requests\SupplierChangePasswordRequest;
use App\Modules\Supplier\Auth\Requests\SupplierForgotPasswordRequest;
use App\Modules\Supplier\Auth\Requests\SupplierLoginRequest;
use App\Modules\Supplier\Auth\Requests\SupplierProfileUpdateRequest;
use App\Modules\Supplier\Auth\Requests\SupplierResetPasswordRequest;
use App\Modules\Supplier\Auth\Resources\SupplierAuthResource;
use App\Modules\Supplier\Auth\Services\SupplierAuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplierAuthController extends Controller
{
    public function __construct(private readonly SupplierAuthService $service) {}

    public function login(SupplierLoginRequest $request)
    {
        $result = $this->service->login($request->string('email')->toString(), $request->string('password')->toString());
        return ApiResponse::success([
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'user' => new SupplierAuthResource($result['user']),
        ], 'Supplier login successful.');
    }

    public function logout(Request $request)
    {
        $this->service->logout($request->user());
        return ApiResponse::success([], 'Supplier logged out successfully.');
    }

    public function forgotPassword(SupplierForgotPasswordRequest $request)
    {
        $status = $this->service->forgotPassword($request->string('email')->toString());
        return ApiResponse::success(['status' => $status], 'If the supplier account exists, a password reset link has been sent.');
    }

    public function resetPassword(SupplierResetPasswordRequest $request)
    {
        $status = $this->service->resetPassword($request->validated());

        if ($status !== 'passwords.reset') {
            throw ValidationException::withMessages(['email' => [__($status)]]);
        }

        return ApiResponse::success([], 'Supplier password reset successfully.');
    }

    public function changePassword(SupplierChangePasswordRequest $request)
    {
        $this->service->changePassword(
            $request->user(),
            $request->string('current_password')->toString(),
            $request->string('new_password')->toString()
        );

        return ApiResponse::success([], 'Supplier password changed successfully. Please login again.');
    }

    public function profile(Request $request)
    {
        return ApiResponse::success(
            new SupplierAuthResource($this->service->profile($request->user())),
            'Supplier profile fetched successfully.'
        );
    }

    public function updateProfile(SupplierProfileUpdateRequest $request)
    {
        return ApiResponse::success(
            new SupplierAuthResource($this->service->updateProfile($request->user(), $request->validated())),
            'Supplier profile updated successfully.'
        );
    }
}
