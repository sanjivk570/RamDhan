<?php
namespace App\Modules\Purchase\Requests;
use App\Core\Requests\BaseRequest;
class PurchasePaymentListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "supplier_id" => ["nullable", "integer"],
            "invoice_uuid" => ["nullable", "uuid"],
            "payment_method" => ["nullable", "string", "max:40"],
            "per_page" => ["nullable", "integer", "min:10", "max:100"],
        ];
    }
}
