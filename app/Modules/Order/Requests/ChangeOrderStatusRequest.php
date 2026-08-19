<?php

declare(strict_types=1);

namespace App\Modules\Order\Requests;
use App\Core\Requests\BaseRequest;
final class ChangeOrderStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "status" => ["required", "string", "max:30"],
            "note" => ["nullable", "string", "max:1000"],
        ];
    }
}
