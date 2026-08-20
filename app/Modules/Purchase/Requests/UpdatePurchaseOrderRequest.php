<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;

final class UpdatePurchaseOrderRequest extends CreatePurchaseOrderRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
