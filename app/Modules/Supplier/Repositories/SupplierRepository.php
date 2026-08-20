<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Repositories;

use App\Modules\Supplier\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository
{
    /**
     * Retrieve paginated suppliers.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Supplier::query()

            /*
             * Global search.
             */
            ->when($filters["search"] ?? null, function (
                $query,
                $search
            ): void {
                $query->where(function ($q) use ($search): void {
                    $q->where("supplier_code", "LIKE", "%{$search}%")

                        ->orWhere("company_name", "LIKE", "%{$search}%")

                        ->orWhere("contact_person", "LIKE", "%{$search}%")

                        ->orWhere("email", "LIKE", "%{$search}%")

                        ->orWhere("mobile", "LIKE", "%{$search}%")

                        ->orWhere("gstin", "LIKE", "%{$search}%")

                        ->orWhere("pan", "LIKE", "%{$search}%");
                });
            })

            /*
             * Column filters.
             */
            ->when(!empty($filters["filters"]["supplier_code"]), function (
                $query
            ) use ($filters): void {
                $query->where(
                    "supplier_code",
                    "LIKE",
                    "%" . $filters["filters"]["supplier_code"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["company_name"]), function (
                $query
            ) use ($filters): void {
                $query->where(
                    "company_name",
                    "LIKE",
                    "%" . $filters["filters"]["company_name"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["contact_person"]), function (
                $query
            ) use ($filters): void {
                $query->where(
                    "contact_person",
                    "LIKE",
                    "%" . $filters["filters"]["contact_person"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["email"]), function ($query) use (
                $filters
            ): void {
                $query->where(
                    "email",
                    "LIKE",
                    "%" . $filters["filters"]["email"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["mobile"]), function (
                $query
            ) use ($filters): void {
                $query->where(
                    "mobile",
                    "LIKE",
                    "%" . $filters["filters"]["mobile"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["gstin"]), function ($query) use (
                $filters
            ): void {
                $query->where(
                    "gstin",
                    "LIKE",
                    "%" . $filters["filters"]["gstin"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["pan"]), function ($query) use (
                $filters
            ): void {
                $query->where(
                    "pan",
                    "LIKE",
                    "%" . $filters["filters"]["pan"] . "%"
                );
            })

            ->when(
                isset($filters["filters"]["status"]) &&
                    $filters["filters"]["status"] !== "",
                function ($query) use ($filters): void {
                    $query->where(
                        "is_active",
                        (bool) $filters["filters"]["status"]
                    );
                }
            )

            /*
             * Sorting.
             */
            ->orderBy(
                $filters["sort_by"] ?? "created_at",
                $filters["sort_order"] ?? "desc"
            )

            /*
             * Pagination.
             */
            ->paginate($filters["per_page"] ?? 20);
    }

    /**
     * Find supplier by UUID.
     */
    public function findByUuid(string $uuid): ?Supplier
    {
        return Supplier::where("uuid", $uuid)->first();
    }

    /**
     * Find supplier or fail.
     */
    public function findByUuidOrFail(string $uuid): Supplier
    {
        return Supplier::where("uuid", $uuid)->firstOrFail();
    }

    /**
     * Find by supplier code.
     */
    public function findBySupplierCode(string $supplierCode): ?Supplier
    {
        return Supplier::where("supplier_code", $supplierCode)->first();
    }

    /**
     * Create supplier.
     */
    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    /**
     * Update supplier.
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->refresh();
    }

    /**
     * Change status.
     */
    public function changeStatus(Supplier $supplier, bool $status): Supplier
    {
        $supplier->update([
            "is_active" => $status,
        ]);

        return $supplier->refresh();
    }

    /**
     * Soft delete supplier.
     */
    public function delete(Supplier $supplier): bool
    {
        return (bool) $supplier->delete();
    }

    /**
     * Restore supplier.
     */
    public function restore(string $uuid): Supplier
    {
        $supplier = Supplier::withTrashed()
            ->where("uuid", $uuid)
            ->firstOrFail();

        $supplier->restore();

        return $supplier->refresh();
    }
}
