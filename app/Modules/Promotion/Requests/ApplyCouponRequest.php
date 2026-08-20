<?php

declare(strict_types=1);

namespace App\Modules\Promotion\Requests;
use App\Core\Requests\BaseRequest;
final class ApplyCouponRequest extends BaseRequest
{
    public function rules(): array
    {
        return ["code" => ["required", "string", "max:100"]];
    }
}
