<?php

declare(strict_types=1);

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Models\Customer;
use App\Modules\Customer\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    public function __construct(private readonly CustomerRepository $repository)
    {
    }

    public function list(array $filters)
    {
        return $this->repository->paginate($filters);
    }

    public function details(string $uuid): Customer
    {
        return $this->repository->findByUuidOrFail($uuid);
    }

    public function create(array $data): Customer
    {
        return $this->repository->create($data);
    }

    public function update(string $uuid, array $data): Customer
    {
        $customer = $this->repository->findByUuidOrFail($uuid);

        return $this->repository->update($customer, $data);
    }

    public function changeStatus(string $uuid, bool $status): Customer
    {
        $customer = $this->repository->findByUuidOrFail($uuid);

        return $this->repository->changeStatus($customer, $status);
    }

    public function delete(string $uuid): void
    {
        $customer = $this->repository->findByUuidOrFail($uuid);

        $this->repository->delete($customer);
    }

    public function restore(string $uuid): Customer
    {
        return $this->repository->restore($uuid);
    }

    public function authenticate(string $email, string $password): ?Customer
    {
        $customer = $this->repository->findByEmail($email);

        if (!$customer) {
            return null;
        }

        if (!$customer->is_active) {
            return null;
        }

        if (!Hash::check($password, $customer->password)) {
            return null;
        }

        return $this->repository->updateLastLogin($customer);
    }

    // public function updateProfile(Customer $customer, array $data): Customer
    // {
    //     return $this->repository->update($customer, $data);
    // }

    public function updateProfile(string $uuid, array $data): Customer
    {
        $customer = $this->repository->findByUuidOrFail($uuid);
        return $this->repository->update($customer, $data);
    }

    public function changePassword(
        Customer $customer,
        string $password
    ): Customer {
        return $this->repository->update($customer, [
            "password" => $password,
        ]);
    }
}
