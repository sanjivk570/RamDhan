<?php
namespace App\Modules\Purchase\Requests;
use App\Core\Requests\BaseRequest;
class PurchaseInvoiceListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "search" => ["nullable", "string", "max:100"],
            "supplier_id" => ["nullable", "integer"],
            "status" => ["nullable", "in:draft,posted,paid,cancelled"],
            "per_page" => ["nullable", "integer", "min:10", "max:100"],
            "sort_by" => [
                "nullable",
                "in:created_at,invoice_date,due_date,grand_total",
            ],
            "sort_order" => ["nullable", "in:asc,desc"],
        ];
    }
}
