<?php

declare(strict_types=1);

namespace App\Modules\Supplier\Requests;

use App\Core\Requests\BaseRequest;

class ChangeSupplierStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "status" => ["required", "boolean"],
        ];
    }
}
