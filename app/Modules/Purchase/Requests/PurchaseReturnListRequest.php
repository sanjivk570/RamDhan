<?php
namespace App\Modules\Purchase\Requests;
use App\Core\Requests\BaseRequest;
class PurchaseReturnListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "supplier_id" => ["nullable", "integer"],
            "status" => ["nullable", "in:draft,posted,cancelled"],
            "per_page" => ["nullable", "integer", "min:10", "max:100"],
        ];
    }
}
