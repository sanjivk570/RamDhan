<?php

declare(strict_types=1);

namespace App\Modules\Purchase\Requests;

use App\Core\Requests\BaseRequest;

final class ReceiptVoidRequest extends BaseRequest
{
    public function rules(): array { return ['reason' => ['required','string','max:2000']]; }
}
