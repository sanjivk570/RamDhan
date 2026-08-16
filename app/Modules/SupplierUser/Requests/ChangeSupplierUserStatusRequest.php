<?php

declare(strict_types=1);

namespace App\Modules\SupplierUser\Requests;

use App\Core\Requests\BaseRequest;

final class ChangeSupplierUserStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return ['status' => ['required', 'boolean']];
    }
}
