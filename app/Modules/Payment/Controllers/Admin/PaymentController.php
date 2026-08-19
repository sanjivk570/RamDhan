<?php

declare(strict_types=1);

namespace App\Modules\Payment\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Actions\AdminListPaymentTransactionsAction;
use App\Modules\Payment\Actions\RefundPaymentAction;
use App\Modules\Payment\Resources\PaymentTransactionResource;
use Illuminate\Http\Request;

/** Administrative payment and refund endpoints. */
final class PaymentController extends Controller
{
    public function __construct(
        private readonly AdminListPaymentTransactionsAction $transactionsAction,
        private readonly RefundPaymentAction $refundAction,
    ) {}

    public function transactions(Request $request)
    {
        $transactions = $this->transactionsAction->execute($request->all());

        return ApiResponse::paginated(
            $transactions,
            PaymentTransactionResource::collection($transactions),
            'Payment transactions fetched successfully.'
        );
    }

    public function refund(Request $request, string $orderUuid)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::where('uuid', $orderUuid)->firstOrFail();

        return ApiResponse::success(
            $this->refundAction->execute(
                $order,
                (float) $data['amount'],
                $data['reason'] ?? '',
            ),
            'Refund requested successfully.'
        );
    }
}
