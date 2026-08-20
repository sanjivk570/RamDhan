<?php

declare(strict_types=1);

namespace App\Modules\Customer\Repositories;

use App\Modules\Customer\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Customer::query()

            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("first_name", "LIKE", "%{$search}%")
                        ->orWhere("last_name", "LIKE", "%{$search}%")
                        ->orWhere("email", "LIKE", "%{$search}%")
                        ->orWhere("mobile", "LIKE", "%{$search}%")
                        ->orWhere("customer_code", "LIKE", "%{$search}%");
                });
            })

            ->when(
                !empty($filters["filters"]["first_name"]),
                fn($query) => $query->where(
                    "first_name",
                    "LIKE",
                    "%" . $filters["filters"]["first_name"] . "%"
                )
            )

            ->when(
                !empty($filters["filters"]["last_name"]),
                fn($query) => $query->where(
                    "last_name",
                    "LIKE",
                    "%" . $filters["filters"]["last_name"] . "%"
                )
            )

            ->when(
                !empty($filters["filters"]["email"]),
                fn($query) => $query->where(
                    "email",
                    "LIKE",
                    "%" . $filters["filters"]["email"] . "%"
                )
            )

            ->when(
                !empty($filters["filters"]["mobile"]),
                fn($query) => $query->where(
                    "mobile",
                    "LIKE",
                    "%" . $filters["filters"]["mobile"] . "%"
                )
            )

            ->when(
                isset($filters["filters"]["status"]) &&
                    $filters["filters"]["status"] !== "",
                fn($query) => $query->where(
                    "is_active",
                    (bool) $filters["filters"]["status"]
                )
            )

            ->orderBy(
                $filters["sort_by"] ?? "created_at",
                $filters["sort_order"] ?? "desc"
            )

            ->paginate($filters["per_page"] ?? 20);
    }

    public function findByUuidOrFail(string $uuid): Customer
    {
        return Customer::query()
            ->with("addresses")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where("email", $email)->first();
    }

    public function findByEmailOrFail(string $email): Customer
    {
        return Customer::where("email", $email)->firstOrFail();
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    // public function update(Customer $customer, array $data): Customer
    // {
    //     $customer->update($data);

    //     return $customer->refresh();
    // }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->refresh();
    }

    public function changeStatus(Customer $customer, bool $status): Customer
    {
        $customer->update([
            "is_active" => $status,
        ]);

        return $customer->refresh();
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }

    public function restore(string $uuid): Customer
    {
        $customer = Customer::withTrashed()
            ->where("uuid", $uuid)
            ->firstOrFail();

        $customer->restore();

        return $customer->refresh();
    }

    public function updateLastLogin(Customer $customer): Customer
    {
        $customer->update([
            "last_login_at" => now(),
        ]);

        return $customer->refresh();
    }
}
