<?php

declare(strict_types=1);

namespace App\Modules\Cart\Resources; use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource; final class CartResource extends JsonResource { public function toArray(Request $request):array{ return [
                'uuid' => $this->uuid,
        'status' => $this->status,
        'currency_code' => $this->currency_code,
        'subtotal' => $this->subtotal,
        'discount_amount' => $this->discount_amount,
        'tax_amount' => $this->tax_amount,
        'shipping_amount' => $this->shipping_amount,
        'grand_total' => $this->grand_total,
        'guest_token' => $this->guest_token,
        'items' => $this->whenLoaded('items'),
 ]; } }
