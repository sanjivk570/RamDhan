<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Requests;
use App\Core\Requests\BaseRequest;
final class ProcessSalesReturnRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "action" => ["required", "in:approve,reject,receive"],
            "admin_note" => ["nullable", "string", "max:2000"],
        ];
    }
}
