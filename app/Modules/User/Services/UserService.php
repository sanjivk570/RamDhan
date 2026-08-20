<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;

/**
 * Service responsible for user-related business logic.
 *
 * Handles user operations including listing, creating,
 * updating, changing status, deleting, and restoring users.
 * Delegates database operations to the user repository.
 *
 * @package App\Modules\User\Services
 * @author Sanjiv Kumar Kushwaha
 */
class UserService
{
    /**
     * Create a new service instance.
     *
     * @param UserRepository $userRepository The user repository.
     */
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * Retrieve a paginated list of users.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return mixed
     */
    public function list(array $filters)
    {
        return $this->userRepository->paginate($filters);
    }

    /**
     * Retrieve user details by UUID.
     *
     * @param string $uuid The user UUID.
     * @return User
     */
    public function details(string $uuid)
    {
        return $this->userRepository->findByUuidOrFail($uuid);
    }

    /**
     * Create a new user and assign a role.
     *
     * Extracts the role information from the request data,
     * creates the user record, and assigns the selected role.
     *
     * @param array<string, mixed> $data The user data.
     * @return User
     */
    public function create(array $data): User
    {
        $role = $data['role'];
        unset($data['role']);
        $user = $this->userRepository->create($data);
        $user->assignRole($role);
        return $user;
    }

    /**
     * Update an existing user.
     *
     * Updates user details and synchronizes the role
     * if a new role is provided.
     *
     * @param string $uuid The user UUID.
     * @param array<string, mixed> $data The updated user data.
     * @return User
     */
    public function update(string $uuid, array $data): User
    {
        $user = $this->userRepository->findByUuidOrFail($uuid);
        $role = $data['role'] ?? null;
        unset($data['role']);
        $user = $this->userRepository->update($user, $data);
        if ($role) {
            $user->syncRoles([$role]);
        }
        return $user;
    }

    /**
     * Change the active status of a user.
     *
     * @param string $uuid The user UUID.
     * @param bool $status The new status value.
     * @return User
     */
    public function changeStatus(string $uuid, bool $status): User 
    {
        $user = $this->userRepository
            ->findByUuidOrFail($uuid);

        return $this->userRepository
            ->changeStatus($user, $status);
    }

    /**
     * Delete a user.
     *
     * Performs a soft delete on the specified user.
     *
     * @param string $uuid The user UUID.
     * @return void
     */
    public function delete(string $uuid): void
    {
        $user = $this->userRepository
            ->findByUuidOrFail($uuid);

        $this->userRepository
            ->delete($user);
    }

    /**
     * Restore a soft-deleted user.
     *
     * @param string $uuid The user UUID.
     * @return User
     */
    public function restore(string $uuid): User
    {
        return $this->userRepository
            ->restore($uuid);
    }
}
