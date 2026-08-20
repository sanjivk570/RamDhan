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
        // Verify webhook signature based on provider
        $provider = $request->string('provider')->toString();
        $signature = $request->header('X-Webhook-Signature');
        
        if (!$signature) {
            return ApiResponse::error('Missing webhook signature.', 401);
        }

        // Basic signature verification - can be extended per provider
        if (!$this->verifyWebhookSignature($provider, $request, $signature)) {
            return ApiResponse::error('Invalid webhook signature.', 401);
        }

        $result = $this->webhookAction->execute(
            $provider,
            $request->string('event')->toString(),
            $request->input('payload', []),
            $request->headers->all(),
        );

        return ApiResponse::success($result, 'Payment webhook processed successfully.');
    }

    private function verifyWebhookSignature(string $provider, WebhookRequest $request, string $signature): bool
    {
        // TODO: Implement provider-specific signature verification
        // For now, implement basic HMAC-SHA256 verification
        $secret = match($provider) {
            'stripe' => config('services.stripe.webhook_secret'),
            'razorpay' => config('services.razorpay.webhook_secret'),
            'paypal' => config('services.paypal.webhook_secret'),
            default => null,
        };

        if (!$secret) {
            return false;
        }

        $payload = $request->getContent();
        $computedSignature = hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($computedSignature, $signature);
    }
}
