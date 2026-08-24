<?php

declare(strict_types=1);

namespace App\Modules\Cart\Resources;

use App\Modules\Cart\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Fully recalculated cart summary resource.
 *
 * Exposes the complete price breakdown (subtotal, discount, tax,
 * shipping, grand total) together with the selected shipping method,
 * so the storefront can render the checkout summary without doing any
 * price math of its own.
 *
 * @mixin Cart
 *
 * @package App\Modules\Cart\Resources
 * @author Sanjiv Kumar Kushwaha
 */
final class CartSummaryResource extends JsonResource
{
    /**
     * Transform the cart into a recalculated summary.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->items ?? collect();

        $itemCount = $items->count();
        $totalQuantity = (float) $items->sum('quantity');
        $totalTaxable = (float) $items->sum('tax_amount');

        return [
            "uuid" => $this->uuid,
            "id" => $this->id,
            "status" => $this->status,
            "currency_code" => $this->currency_code,
            "coupon_code" => $this->coupon_code,

            /*
             * Price breakdown.
             */
            "breakdown" => [
                "subtotal" => (float) $this->subtotal,
                "discount_amount" => (float) $this->discount_amount,
                "tax_amount" => (float) $this->tax_amount,
                "shipping_amount" => (float) $this->shipping_amount,
                "grand_total" => (float) $this->grand_total,
                "item_count" => $itemCount,
                "total_quantity" => $totalQuantity,
                "tax_included_in_lines" => $totalTaxable > 0,
            ],

            /*
             * Selected shipping method (null until applied).
             */
            "shipping_method" => $this->when(
                !empty($this->shipping_rate_uuid),
                fn () => [
                    "rate_uuid" => $this->shipping_rate_uuid,
                    "method_uuid" => $this->shipping_method_uuid,
                    "name" => $this->shipping_method_name,
                    "code" => $this->shipping_method_code,
                    "amount" => (float) $this->shipping_amount,
                    "estimated_delivery_min_days" => $this->estimated_delivery_min_days,
                    "estimated_delivery_max_days" => $this->estimated_delivery_max_days,
                ]
            ),

            /*
             * Destination used for shipping + destination-aware tax.
             */
            "shipping_address" => $this->shipping_address,

            /*
             * Line items with their computed prices.
             */
            "items" => $items->map(fn ($item) => [
                "uuid" => $item->uuid,
                "product_name" => $item->product_name,
                "variant_name" => $item->variant_name,
                "sku" => $item->sku,
                "quantity" => (float) $item->quantity,
                "unit_price" => (float) $item->unit_price,
                "compare_price" => (float) $item->compare_price,
                "discount_amount" => (float) $item->discount_amount,
                "tax_rate" => (float) $item->tax_rate,
                "tax_amount" => (float) $item->tax_amount,
                "line_subtotal" => (float) $item->line_subtotal,
                "line_total" => (float) $item->line_total,
            ])->values(),

            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}