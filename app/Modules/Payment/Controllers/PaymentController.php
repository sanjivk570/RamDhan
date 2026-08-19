<?php

declare(strict_types=1);

namespace App\Modules\Payment\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Actions\CreatePaymentIntentAction;
use App\Modules\Payment\Actions\HandlePaymentWebhookAction;
use App\Modules\Payment\Actions\ShowPaymentIntentAction;
use App\Modules\Payment\Requests\CreatePaymentIntentRequest;
use App\Modules\Payment\Requests\WebhookRequest;
use App\Modules\Payment\Resources\PaymentIntentResource;
use Illuminate\Http\Request;

/** Frontend payment and gateway webhook endpoints. */
final class PaymentController extends Controller
{
    public function __construct(
        private readonly CreatePaymentIntentAction $createIntentAction,
        private readonly ShowPaymentIntentAction $showIntentAction,
        private readonly HandlePaymentWebhookAction $webhookAction,
    ) {}

    public function intent(CreatePaymentIntentRequest $request)
    {
        $order = Order::where('uuid', $request->order_uuid)
            ->where(function ($query) use ($request) {
                if ($request->user()) {
                    $query->where('customer_id', $request->user()->id);
                } else {
                    $query->where('guest_token', $request->header('X-Guest-Token'));
                }
            })
            ->firstOrFail();

        $intent = $this->createIntentAction->execute(
            $order,
            $request->string('provider')->toString(),
            $request->string('method')->toString(),
        );

        return ApiResponse::success(
            new PaymentIntentResource($intent),
            'Payment intent created successfully.'
        );
    }

    public function showIntent(Request $request, string $uuid)
    {
        return ApiResponse::success(
            new PaymentIntentResource($this->showIntentAction->execute(
                $request->user()?->id,
                $request->header('X-Guest-Token'),
                $uuid,
            )),
            'Payment intent fetched successfully.'
        );
    }

    public function webhook(WebhookRequest $request)
    {
        $result = $this->webhookAction->execute(
            $request->string('provider')->toString(),
            $request->string('event')->toString(),
            $request->input('payload', []),
            $request->headers->all(),
        );

        return ApiResponse::success($result, 'Payment webhook processed successfully.');
    }
}
