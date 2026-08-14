<?php

declare(strict_types=1);

namespace App\Modules\Customer\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;

use App\Modules\Customer\Services\CustomerAuthService;
use App\Modules\Customer\Requests\CustomerLoginRequest;
use App\Modules\Customer\Requests\CustomerRegisterRequest;
use App\Modules\Customer\Requests\ForgotPasswordRequest;
use App\Modules\Customer\Requests\ResetPasswordRequest;
use App\Modules\Customer\Requests\ChangeCustomerPasswordRequest;
use App\Modules\Customer\Resources\CustomerResource;

class CustomerAuthController extends Controller
{
    public function __construct(
        private readonly CustomerAuthService $authService
    ) {
    }

    public function register(CustomerRegisterRequest $request)
    {
        $customer = $this->authService->register($request->validated());

        $login = $this->authService->login(
            $customer->email,
            $request->string("password")->toString()
        );

        return ApiResponse::success(
            [
                "customer" => new CustomerResource($login["customer"]),

                "token" => $login["token"],

                "token_type" => "Bearer",
            ],
            "Customer registered successfully."
        );
    }

    public function login(CustomerLoginRequest $request)
    {
        $result = $this->authService->login(
            $request->string("email")->toString(),

            $request->string("password")->toString()
        );

        return ApiResponse::success(
            [
                "customer" => new CustomerResource($result["customer"]),

                "token" => $result["token"],

                "token_type" => "Bearer",
            ],
            "Login successful."
        );
    }

    public function logout()
    {
        $customer = auth("customer")->user();

        $this->authService->logout($customer);

        return ApiResponse::success([], "Logout successful.");
    }

    public function profile()
    {
        $customer = auth("customer")->user();

        return ApiResponse::success(
            new CustomerResource($customer),
            "Profile fetched successfully."
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = $this->authService->forgotPassword(
            $request->string("email")->toString()
        );

        return ApiResponse::success(
            [
                "status" => $status,
            ],
            "Password reset link sent successfully."
        );
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = $this->authService->resetPassword($request->validated());

        return ApiResponse::success(
            [
                "status" => $status,
            ],
            "Password reset successfully."
        );
    }

    // public function changePassword(ChangeCustomerPasswordRequest $request)
    // {
    //     $customer = auth("customer")->user();

    //     $this->authService->changePassword($customer, $request->validated());

    //     return ApiResponse::success([], "Password changed successfully.");
    // }

    public function changePassword( ChangeCustomerPasswordRequest $request
    ) {
        $customer = auth('customer')->user();
        $token = $this->authService
                ->changePassword(
                    $customer,
                    $request->validated()
                );

        return ApiResponse::success(
            [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
            'Password changed successfully.'
        );
    }
}
