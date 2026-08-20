<?php
namespace App\Modules\Purchase\Requests;
use App\Core\Requests\BaseRequest;
class CreatePurchasePaymentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "invoice_uuid" => ["required", "uuid"],
            "payment_date" => ["required", "date"],
            "amount" => ["required", "numeric", "gt:0"],
            "currency_code" => ["nullable", "string", "size:3"],
            "payment_method" => [
                "required",
                "in:cash,bank_transfer,upi,card,cheque,neft,rtgs,imps,other",
            ],
            "reference_number" => ["nullable", "string", "max:120"],
            "bank_account" => ["nullable", "string", "max:150"],
            "notes" => ["nullable", "string"],
        ];
    }
}
