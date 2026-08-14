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

    /**
     * Dashoboard permissions.
     */
    case DASHBOARD_VIEW = 'dashboard.view';

    /**
     * Category permissions.
     */
    case CATEGORY_VIEW = 'category.view';
    case CATEGORY_CREATE = 'category.create';
    case CATEGORY_UPDATE = 'category.update';
    case CATEGORY_DELETE = 'category.delete';
    case CATEGORY_RESTORE = 'category.restore';

    /**
     * product permissions.
     */
    case PRODUCT_VIEW = 'product.view';
    case PRODUCT_CREATE = 'product.create';
    case PRODUCT_UPDATE = 'product.update';
    case PRODUCT_DELETE = 'product.delete';
    case PRODUCT_RESTORE = 'product.restore';

    // customer permisions
    case CUSTOMER_VIEW = 'customer.view';
    case CUSTOMER_CREATE = 'customer.create';
    case CUSTOMER_UPDATE = 'customer.update';
    case CUSTOMER_DELETE = 'customer.delete';
    case CUSTOMER_RESTORE = 'customer.restore';
    
}
