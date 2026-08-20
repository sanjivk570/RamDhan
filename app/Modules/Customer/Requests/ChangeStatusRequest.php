<?php

declare(strict_types=1);

namespace App\Modules\Customer\Requests;

use App\Core\Requests\BaseRequest;

class ChangeStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "status" => ["required", "boolean"],
        ];
    }
}
