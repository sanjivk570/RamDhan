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

    // supplier permisions
    case SUPPLIER_VIEW = 'supplier.view';
    case SUPPLIER_CREATE = 'supplier.create';
    case SUPPLIER_UPDATE = 'supplier.update';
    case SUPPLIER_DELETE = 'supplier.delete';
    case SUPPLIER_RESTORE = 'supplier.restore';

    case SUPPLIER_USER_VIEW = 'supplier.user.view';
    case SUPPLIER_USER_CREATE = 'supplier.user.create';
    case SUPPLIER_USER_UPDATE = 'supplier.user.update';
    case SUPPLIER_USER_DELETE = 'supplier.user.delete';

    case SUPPLIER_PURCHASE_VIEW = 'supplier.purchase.view';
    case SUPPLIER_PURCHASE_UPDATE = 'supplier.purchase.update';

    case SUPPLIER_INVOICE_VIEW = 'supplier.invoice.view';
    case SUPPLIER_PAYMENT_VIEW = 'supplier.payment.view';

    //for purchase
    case PURCHASE_VIEW = 'purchase.view';
    case PURCHASE_CREATE = 'purchase.create';
    case PURCHASE_UPDATE = 'purchase.update';
    case PURCHASE_SUBMIT = 'purchase.submit';
    case PURCHASE_APPROVE = 'purchase.approve';
    case PURCHASE_CANCEL = 'purchase.cancel';
    case PURCHASE_GRN_VIEW = 'purchase.grn.view';
    case PURCHASE_GRN_CREATE = 'purchase.grn.create';
    case PURCHASE_GRN_POST = 'purchase.grn.post';
    case PURCHASE_GRN_VOID = 'purchase.grn.void';
    case PURCHASE_INVOICE_VIEW = 'purchase.invoice.view';
    case PURCHASE_INVOICE_CREATE = 'purchase.invoice.create';
    case PURCHASE_INVOICE_POST = 'purchase.invoice.post';
    case PURCHASE_PAYMENT_VIEW = 'purchase.payment.view';
    case PURCHASE_PAYMENT_CREATE = 'purchase.payment.create';
    case PURCHASE_RETURN_VIEW = 'purchase.return.view';
    case PURCHASE_RETURN_CREATE = 'purchase.return.create';
    case PURCHASE_RETURN_POST = 'purchase.return.post';

    //for cart
    case CART_VIEW = "cart.view";

    //For order
    case ORDER_VIEW = 'order.view';
    case ORDER_UPDATE = 'order.update';
    case ORDER_CANCEL = "order.cancel";

    //For Shipment
    case SHIPMENT_VIEW = "shipment.view";
    case SHIPMENT_CREATE = "shipment.create";
    case SHIPMENT_UPDATE = "shipment.update";

    //for Invoice
    case INVOICE_VIEW = "invoice.view";
    case INVOICE_CREATE =  "invoice.create";

    //for payment
    case PAYMENT_VIEW = "payment.view"; 
    case PAYMENT_REFUND = "payment.refund";

    //For return
    case RETURN_VIEW = "return.view";
    case RETURN_PROCESS = "return.process";
    
}
