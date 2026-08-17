# RamDhan Purchase Module — Complete Professional

This package contains the purchase lifecycle designed for a startup/ERP/e-commerce backend:

```text
Supplier
  ↓
Purchase Order (PO)
  ↓
Submit → Approve
  ↓
Goods Receipt Note (GRN)
  ↓
Accepted Qty
  ↓
Inventory Stock IN + Inventory Transaction
  ↓
Purchase Invoice (supplier bill)
  ↓
Purchase Payment
  ↓
Paid / Due tracking

Supplier returns are handled through Purchase Return → stock-out/reversal integration.
```

## Included

- Purchase Order + items
- PO submit / approve / cancel workflow
- GRN + partial receiving
- Inventory stock-in adapter using existing `inventory_stocks` and `inventory_transactions`
- Purchase Invoice + invoice items
- Purchase Payment + outstanding/due tracking
- Purchase Return + return items
- Permission seeder
- Postman collection

## Important distinction

The original Purchase package had PO + GRN + inventory integration only. It did **not** contain supplier invoice, payment, or purchase-return modules. This package adds those missing transactional layers.

## APIs

### Purchase Orders
GET `/api/v1/purchase-orders`
GET `/api/v1/purchase-orders/{uuid}`
POST `/api/v1/purchase-orders`
PUT `/api/v1/purchase-orders/{uuid}`
PATCH `/api/v1/purchase-orders/{uuid}/submit`
PATCH `/api/v1/purchase-orders/{uuid}/approve`
PATCH `/api/v1/purchase-orders/{uuid}/cancel`

### GRN / Inventory Stock In
GET `/api/v1/goods-receipts`
GET `/api/v1/goods-receipts/{uuid}`
POST `/api/v1/goods-receipts`
PATCH `/api/v1/goods-receipts/{uuid}/post`
PATCH `/api/v1/goods-receipts/{uuid}/void`

### Supplier Purchase Invoice
GET `/api/v1/purchase-invoices`
GET `/api/v1/purchase-invoices/{uuid}`
POST `/api/v1/purchase-invoices`
PATCH `/api/v1/purchase-invoices/{uuid}/post`

### Supplier Payment
GET `/api/v1/purchase-payments`
GET `/api/v1/purchase-payments/{uuid}`
POST `/api/v1/purchase-payments`

### Purchase Return
GET `/api/v1/purchase-returns`
GET `/api/v1/purchase-returns/{uuid}`
POST `/api/v1/purchase-returns`
PATCH `/api/v1/purchase-returns/{uuid}/post`

## Production notes

1. Keep one inventory ledger. Do not create a second stock system inside Purchase.
2. GRN posting is the stock-in event. Creating a PO must never increase stock.
3. Invoice posting creates the supplier liability; payment reduces the outstanding balance.
4. Payment amount is validated against invoice due amount.
5. Use InnoDB for all transactional tables before production so cross-module DB transactions are atomic.
6. For accounting-grade deployments, the next layer should be a dedicated Accounting/Ledger module rather than storing journal entries inside Purchase.
7. Purchase Return posting should be connected to the existing inventory service so accepted returned quantity creates a compensating stock-out transaction.

## Install

```bash
php artisan migrate
php artisan db:seed --class="App\\Modules\\Purchase\\Seeders\\PurchasePermissionSeeder"
php artisan optimize:clear
```

Register `App\\Modules\\Purchase\\Providers\\PurchaseServiceProvider::class` if module providers are not auto-discovered.
