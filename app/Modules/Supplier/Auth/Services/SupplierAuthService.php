<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Auth\Services;

use App\Modules\User\Models\User;
//use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SupplierAuthService
{
    public function login(string $email, string $password): array
    {
        $user = User::with(['supplier', 'roles'])
            ->where('email', $email)
            ->where('user_type', 'supplier')
            ->whereNotNull('supplier_id')
            ->first();
        
        if (!$user || !Hash::check($password, $user->password)) {
            throw new UnauthorizedHttpException('', 'Invalid supplier credentials.');
        }

        if (!$user->is_active || !$user->supplier?->is_active) {
            throw new UnauthorizedHttpException('', 'Supplier account is inactive.');
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $user->tokens()->delete();
        $token = $user->createToken('supplier-portal')->plainTextToken;

        return ['user' => $user->fresh(['supplier', 'roles']), 'token' => $token];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();
        if ($token) {
            $token->delete();
            return;
        }

        $user->tokens()->delete();
    }

    public function profile(User $user): User
    {
        return $user->load(['supplier', 'roles']);
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new UnauthorizedHttpException('', 'Current password is incorrect.');
        }

        $user->forceFill([
            'password' => $newPassword,
            'remember_token' => Str::random(60),
        ])->save();

        $user->tokens()->delete();
    }

    public function forgotPassword(string $email): string
    {
        $user = User::where('email', $email)->where('user_type', 'supplier')->whereNotNull('supplier_id')->first();

        // Do not reveal whether a supplier account exists.
        if (!$user) {
            return Password::RESET_LINK_SENT;
        }

        return Password::broker()->sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $data): string
    {
        $supplierUserExists = User::where('email', $data['email'])
            ->where('user_type', 'supplier')
            ->whereNotNull('supplier_id')
            ->exists();

        if (!$supplierUserExists) {
            return Password::INVALID_USER;
        }

        return Password::broker()->reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                //'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function (User $user, string $password): void {
                if ($user->user_type !== 'supplier' || !$user->supplier_id) {
                    return;
                }

                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
                event(new PasswordReset($user));
            }
        );
    }

    public function updateProfile(User $user, array $data): User
    {
        $userData = array_intersect_key($data, array_flip([
            'first_name', 'last_name', 'mobile', 'country_code',
        ]));

        $supplierData = [];
        $mapping = [
            'company_name' => 'company_name',
            'contact_person' => 'contact_person',
            'supplier_email' => 'email',
            'supplier_mobile' => 'mobile',
            'alternate_mobile' => 'alternate_mobile',
            'website' => 'website',
            'gstin' => 'gstin',
            'pan' => 'pan',
            'payment_terms_days' => 'payment_terms_days',
            'credit_limit' => 'credit_limit',
            'notes' => 'notes',
        ];

        foreach ($mapping as $input => $column) {
            if (array_key_exists($input, $data)) {
                $supplierData[$column] = $data[$input];
            }
        }

        if ($userData) {
            $user->update($userData);
        }

        if ($supplierData && $user->supplier) {
            $user->supplier->update($supplierData);
        }

        return $user->fresh(['supplier', 'roles']);
    }
}
