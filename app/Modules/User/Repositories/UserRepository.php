<?php

declare(strict_types=1);

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for user data access operations.
 *
 * Handles database interactions related to users including
 * searching, filtering, creating, updating, deleting,
 * restoring, and retrieving user records.
 *
 * @package App\Modules\User\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
class UserRepository
{
    /**
     * Retrieve paginated users with optional filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return User::query()->with('roles')

            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {

                    $query->where(function ($q) use ($search) {

                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('mobile', 'LIKE', "%{$search}%");
                    });
                }
            )

            // ->when(
            //     isset($filters['status']),
            //     function ($query) use ($filters) {
            //         $query->where(
            //             'is_active',
            //             $filters['status']
            //         );
            //     }
            // )

            // Column Filters
            ->when(
                !empty($filters['filters']['first_name']),
                function ($query) use ($filters) {

                    $query->where(
                        'first_name',
                        'LIKE',
                        '%' . $filters['filters']['first_name'] . '%'
                    );

                }
            )

            ->when(
                !empty($filters['filters']['last_name']),
                function ($query) use ($filters) {

                    $query->where(
                        'last_name',
                        'LIKE',
                        '%' . $filters['filters']['last_name'] . '%'
                    );

                }
            )

            ->when(
                !empty($filters['filters']['email']),
                function ($query) use ($filters) {

                    $query->where(
                        'email',
                        'LIKE',
                        '%' . $filters['filters']['email'] . '%'
                    );

                }
            )

            ->when(
                !empty($filters['filters']['mobile']),
                function ($query) use ($filters) {

                    $query->where(
                        'mobile',
                        'LIKE',
                        '%' . $filters['filters']['mobile'] . '%'
                    );

                }
            )

            ->when(
                isset($filters['filters']['status']) &&
                $filters['filters']['status'] !== '',
                function ($query) use ($filters) {

                    $query->where(
                        'is_active',
                        (bool) $filters['filters']['status']
                    );

                }
            )

            ->orderBy(
                $filters['sort_by'] ?? 'created_at',
                $filters['sort_order'] ?? 'desc'
            )

            ->paginate(
                $filters['per_page'] ?? 20
            );
    }

    /**
     * Find a user by database ID.
     *
     * @param int $id The user ID.
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by UUID.
     *
     * @param string $uuid The user UUID.
     * @return User|null
     */
    public function findByUuid(string $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }

    /**
     * Find a user by email address.
     *
     * @param string $email The user email address.
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     *
     * @param array<string, mixed> $data The user data.
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     *
     * @param User $user The user instance.
     * @param array<string, mixed> $data The updated user data.
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    /**
     * Find a user by UUID or throw an exception.
     *
     * @param string $uuid The user UUID.
     * @return User
     */
    public function findByUuidOrFail(string $uuid): User
    {
        return User::with('roles')->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Change the active status of a user.
     *
     * @param User $user The user instance.
     * @param bool $status The new status value.
     * @return User
     */
    public function changeStatus(User $user, bool $status): User
    {
        $user->update([
            'is_active' => $status
        ]);

        return $user->refresh();
    }

    /**
     * Soft delete a user.
     *
     * @param User $user The user instance.
     * @return bool
     */
    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param string $uuid The user UUID.
     * @return User
     */
    public function restore(string $uuid): User
    {
        $user = User::withTrashed()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $user->restore();

        return $user->refresh();
    }    
}