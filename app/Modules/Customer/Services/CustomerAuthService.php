<?php

declare(strict_types=1);

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomerAuthService
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    public function register(array $data): Customer
    {
        $customer = $this->customerService->create($data);

        return $customer;
    }

    public function login(string $email, string $password): array
    {
        $customer = $this->customerService->authenticate($email, $password);

        if (!$customer) {
            throw ValidationException::withMessages([
                "email" => ["Invalid email or password."],
            ]);
        }

        $token = $customer->createToken("customer-web")->plainTextToken;

        return [
            "customer" => $customer,
            "token" => $token,
        ];
    }

    // public function logout(Customer $customer): void
    // {
    //     $token = $customer->currentAccessToken();

    //     if ($token) {
    //         $token->delete();
    //     }
    // }

    public function logout(Customer $customer): void
    {
        $token = $customer->currentAccessToken();

        if ($token && method_exists($token, 'delete')) {
            $token->delete();

            return;
        }

        if (method_exists($customer, 'tokens')) {
            $customer->tokens()->delete();
        }
    }


    public function forgotPassword(string $email): string
    {
        // return Password::broker("customers")->sendResetLink([
        //     "email" => $email,
        // ]);

        $status =  Password::broker("customers")->sendResetLink([
            "email" => $email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new HttpException(
                422,
                __($status)
            );
        }
        return $status;
    }

    public function resetPassword(array $data): string
    {
        return Password::broker("customers")->reset(
            [
                "email" => $data["email"],
                "password" => $data["password"],
                //"password_confirmation" => $data["password_confirmation"],
                "token" => $data["token"],
            ],
            function (Customer $customer, string $password) {
                $customer
                    ->forceFill([
                        "password" => $password,
                        "remember_token" => \Illuminate\Support\Str::random(60),
                    ])
                    ->save();

                $customer->tokens()->delete();
            }
        );
    }

    public function changePassword(Customer $customer, array $data): string {

        $customer->update([
            'password' => $data['new_password'],
        ]);

        $customer->tokens()->delete();

        return $customer
            ->createToken('customer-web')
            ->plainTextToken;
    }
}
