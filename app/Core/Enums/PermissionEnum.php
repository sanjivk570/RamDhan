<?php

declare(strict_types=1);

namespace App\Core\Enums;

/**
 * Define application permission identifiers.
 *
 * This enum contains all permission keys used for role-based
 * access control (RBAC) throughout the application.
 *
 * @package App\Core\Enums
 * @author Sanjiv Kumar Kushwaha
 */
enum PermissionEnum: string
{
    /**
     * Role permissions.
     */
    case ROLE_VIEW = 'role.view';
    case ROLE_CREATE = 'role.create';
    case ROLE_UPDATE = 'role.update';
    case ROLE_DELETE = 'role.delete';

    /**
     * User permissions.
     */
    case USER_VIEW = 'user.view';
    case USER_CREATE = 'user.create';
    case USER_UPDATE = 'user.update';
    case USER_DELETE = 'user.delete';
}
