<?php

declare(strict_types=1);

namespace App\Modules\Shipping\Services;

use App\Modules\Shipping\Models\ShippingRate;
use App\Modules\Shipping\Models\ShippingZone;
use App\Modules\Shipping\Repositories\ShippingMethodRepository;
use App\Modules\Shipping\Repositories\ShippingRateRepository;
use App\Modules\Shipping\Repositories\ShippingZoneRepository;
use Illuminate\Support\Collection;

use App\Modules\Cart\Models\Cart;
use App\Modules\Customer\Models\CustomerAddress;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShippingService
{
    public function __construct(
        private readonly ShippingZoneRepository $zoneRepository,
        private readonly ShippingMethodRepository $methodRepository,
        private readonly ShippingRateRepository $rateRepository
    ) {
    }

    /*
     * ---------------------------------------------------------
     * Zone
     * ---------------------------------------------------------
     */

    public function listZones(array $filters)
    {
        return $this->zoneRepository->paginate($filters);
    }

    public function createZone(array $data)
    {
        return $this->zoneRepository->create($data);
    }

    public function updateZone(string $uuid, array $data)
    {
        $zone = $this->zoneRepository->findByUuidOrFail($uuid);

        return $this->zoneRepository->update($zone, $data);
    }

    public function deleteZone(string $uuid): void
    {
        $zone = $this->zoneRepository->findByUuidOrFail($uuid);

        $this->zoneRepository->delete($zone);
    }

    /*
     * ---------------------------------------------------------
     * Methods
     * ---------------------------------------------------------
     */

    public function listMethods(array $filters)
    {
        return $this->methodRepository->paginate($filters);
    }

    public function createMethod(array $data)
    {
        return $this->methodRepository->create($data);
    }

    public function updateMethod(string $uuid, array $data)
    {
        $method = $this->methodRepository->findByUuidOrFail($uuid);

        return $this->methodRepository->update($method, $data);
    }

    public function deleteMethod(string $uuid): void
    {
        $method = $this->methodRepository->findByUuidOrFail($uuid);

        $this->methodRepository->delete($method);
    }

    /*
     * ---------------------------------------------------------
     * Rates
     * ---------------------------------------------------------
     */

    public function listRates(array $filters)
    {
        return $this->rateRepository->paginate($filters);
    }

    public function createRate(array $data)
    {
        return $this->rateRepository->create($data);
    }

    public function updateRate(string $uuid, array $data)
    {
        $rate = $this->rateRepository->findByUuidOrFail($uuid);

        return $this->rateRepository->update($rate, $data);
    }

    public function deleteRate(string $uuid): void
    {
        $rate = $this->rateRepository->findByUuidOrFail($uuid);

        $this->rateRepository->delete($rate);
    }

    /*
     * ---------------------------------------------------------
     * Calculate Delivery Rates
     * ---------------------------------------------------------
     */

    public function calculateRates(array $data): Collection
    {
        $countryCode = strtoupper((string) $data["country_code"]);

        $stateCode = strtoupper((string) ($data["state_code"] ?? ""));

        $postalCode = (string) $data["postal_code"];

        $orderAmount = (float) $data["order_amount"];

        $weight = (float) $data["weight"];

        $zones = ShippingZone::query()
            ->where("is_active", true)
            ->orderBy("sort_order")
            ->get();

        /*
         * Collect EVERY zone whose destination rules match.
         *
         * A destination can legitimately be covered by more than one
         * zone (e.g. a country-level zone plus a state-level zone), so
         * restricting to the first match silently drops valid rates.
         */
        $matchedZoneIds = $zones
            ->filter(
                fn (ShippingZone $zone) => $this->zoneMatches(
                    $zone,
                    $countryCode,
                    $stateCode,
                    $postalCode
                )
            )
            ->pluck("id");

        if ($matchedZoneIds->isEmpty()) {
            return collect();
        }

        $rates = ShippingRate::query()
            ->with("method")
            ->whereIn("shipping_zone_id", $matchedZoneIds)
            ->where("is_active", true)
            ->where(function ($query) use ($weight) {
                $query
                    ->whereNull("min_weight")
                    ->orWhere("min_weight", "<=", $weight);
            })
            ->where(function ($query) use ($weight) {
                $query
                    ->whereNull("max_weight")
                    ->orWhere("max_weight", ">=", $weight);
            })
            ->where(function ($query) use ($orderAmount) {
                $query
                    ->whereNull("min_order_amount")
                    ->orWhere("min_order_amount", "<=", $orderAmount);
            })
            ->where(function ($query) use ($orderAmount) {
                $query
                    ->whereNull("max_order_amount")
                    ->orWhere("max_order_amount", ">=", $orderAmount);
            })
            ->orderBy("sort_order")
            ->get();

        /*
         * A rate whose delivery method is missing or inactive must
         * never break the storefront - skip it defensively instead of
         * letting a single bad row turn into a 500.
         */
        $rates = $rates->filter(
            fn (ShippingRate $rate) => $rate->method !== null
                && $rate->method->is_active
        );

        return $rates->map(function (ShippingRate $rate) use (
            $orderAmount,
            $weight
        ) {
            $shippingAmount = $this->calculateRate(
                $rate,
                $orderAmount,
                $weight
            );

            return [
                "uuid" => $rate->uuid,

                "method" => [
                    "uuid" => $rate->method->uuid,
                    "name" => $rate->method->name,
                    "code" => $rate->method->code,
                ],

                "shipping_amount" => round($shippingAmount, 2),

                "currency" => "INR",

                "delivery" => [
                    "min_days" => $rate->method->min_delivery_days,

                    "max_days" => $rate->method->max_delivery_days,
                ],

                "is_free" => $shippingAmount <= 0,
            ];
        });
    }

    private function calculateRate(
        ShippingRate $rate,
        float $orderAmount,
        float $weight
    ): float {
        if (
            $rate->free_shipping_threshold !== null &&
            $orderAmount >= (float) $rate->free_shipping_threshold
        ) {
            return 0;
        }

        $amount = (float) $rate->base_rate;

        if ((float) $rate->per_kg_rate > 0 && $weight > 0) {
            $amount += $weight * (float) $rate->per_kg_rate;
        }

        return max(0, $amount);
    }

    private function zoneMatches(
        ShippingZone $zone,
        string $countryCode,
        string $stateCode,
        string $postalCode
    ): bool {
        $countries = array_map("strtoupper", $zone->countries ?? []);

        if (!empty($countries) && !in_array($countryCode, $countries, true)) {
            return false;
        }

        $states = array_map("strtoupper", $zone->states ?? []);

        if (!empty($states) && !in_array($stateCode, $states, true)) {
            return false;
        }

        $postalCodes = $zone->postal_codes ?? [];

        if (
            !empty($postalCodes) &&
            !in_array($postalCode, $postalCodes, true)
        ) {
            return false;
        }

        return true;
    }

    /*
     * Existing constructor/dependencies
     * remain here.
     */

    /**
     * Calculate shipping rates for a real cart.
     *
     * Supports both authenticated customers (saved address) and
     * guests (inline destination). The resolved destination is
     * snapshotted on the cart so tax, shipping selection, summary
     * and checkout all reuse the same destination.
     *
     * @param string $cartUuid
     * @param string|null $customerAddressUuid Saved customer address UUID.
     * @param int|null $customerId Authenticated customer id.
     * @param array<string, mixed> $inlineAddress Guest inline destination.
     * @param string|null $guestToken Guest cart token.
     * @return array<string, mixed>
     */
    public function calculateCartShipping(
        string $cartUuid,
        ?string $customerAddressUuid = null,
        ?int $customerId = null,
        array $inlineAddress = [],
        ?string $guestToken = null
    ): array {
        return DB::transaction(function () use (
            $cartUuid,
            $customerAddressUuid,
            $customerId,
            $inlineAddress,
            $guestToken
        ) {

            /*
             * ---------------------------------------------------------
             * 1. Load cart
             * ---------------------------------------------------------
             */

            $cart = Cart::query()
                ->with([
                    'items.product',
                    'items.variant',
                ])
                ->where('uuid', $cartUuid)
                ->where('status', 'active')
                ->first();

            if (!$cart) {
                throw (new ModelNotFoundException())
                    ->setModel(Cart::class, [$cartUuid]);
            }

            /*
             * ---------------------------------------------------------
             * 2. Security: only the cart owner may use it
             * ---------------------------------------------------------
             */

            $this->authorizeCart($cart, $customerId, $guestToken);

            /*
             * ---------------------------------------------------------
             * 3. Resolve destination (saved address or inline)
             * ---------------------------------------------------------
             */

            $address = $this->resolveCartShippingAddress(
                $cart,
                $customerAddressUuid,
                $inlineAddress
            );

            /*
             * ---------------------------------------------------------
             * 4. Cart amount + weight
             * ---------------------------------------------------------
             */

            [$subtotal, $totalWeight] = $this->cartAmounts($cart);

            if ($cart->items->isEmpty()) {
                return [
                    'cart_uuid' => $cart->uuid,
                    'customer_address_uuid' => $address['uuid'] ?? null,
                    'address' => $address,
                    'currency' => 'INR',
                    'subtotal' => 0,
                    'discount' => 0,
                    'tax' => 0,
                    'order_amount' => 0,
                    'total_weight' => 0,
                    'rates' => [],
                ];
            }

            /*
             * ---------------------------------------------------------
             * 5. Snapshot the destination on the cart so later steps
             *    (apply shipping / summary / checkout) reuse it.
             * ---------------------------------------------------------
             */

            $cart->update(['shipping_address' => $address]);

            /*
             * ---------------------------------------------------------
             * 6. Existing shipping rate calculation
             * ---------------------------------------------------------
             */

            $rates = $this->calculateRates([
                'country_code' => (string) ($address['country_code'] ?? ''),
                'state_code' => (string) ($address['state_code'] ?? ''),
                'postal_code' => (string) ($address['postal_code'] ?? ''),
                'order_amount' => $subtotal,
                'weight' => $totalWeight,
            ]);

            /*
             * ---------------------------------------------------------
             * 7. Response
             * ---------------------------------------------------------
             */

            return [
                'cart_uuid' => $cart->uuid,

                'customer_address_uuid' => $address['uuid'] ?? null,

                'address' => $address,

                'summary' => [
                    'currency' => 'INR',
                    'subtotal' => round($subtotal, 2),
                    'total_weight' => round($totalWeight, 3),
                ],

                'rates' => $rates,
            ];
        });
    }

    /**
     * Apply a selected shipping rate to a cart.
     *
     * Flow: address selected -> shipping rates listed -> shopper picks
     * a rate -> this validates the rate against the destination and the
     * live cart amounts (server-side), stores it on the cart and
     * recalculates all cart prices (tax/shipping/grand total).
     *
     * @param string $cartUuid
     * @param string $rateUuid Selected shipping rate UUID.
     * @param int|null $customerId Authenticated customer id.
     * @param string|null $guestToken Guest cart token.
     * @param string|null $customerAddressUuid Optional saved address override.
     * @param array<string, mixed> $inlineAddress Optional inline destination override.
     * @return \App\Modules\Cart\Models\Cart Recalculated cart.
     *
     * @throws \RuntimeException When the selected rate is not applicable.
     */
    public function applyShippingToCart(
        string $cartUuid,
        string $rateUuid,
        ?int $customerId = null,
        ?string $guestToken = null,
        ?string $customerAddressUuid = null,
        array $inlineAddress = []
    ): Cart {
        return DB::transaction(function () use (
            $cartUuid,
            $rateUuid,
            $customerId,
            $guestToken,
            $customerAddressUuid,
            $inlineAddress
        ) {

            /*
             * ---------------------------------------------------------
             * 1. Load cart + ownership check
             * ---------------------------------------------------------
             */

            $cart = Cart::query()
                ->with(['items.product', 'items.variant'])
                ->where('uuid', $cartUuid)
                ->where('status', 'active')
                ->first();

            if (!$cart) {
                throw (new ModelNotFoundException())
                    ->setModel(Cart::class, [$cartUuid]);
            }

            $this->authorizeCart($cart, $customerId, $guestToken);

            /*
             * ---------------------------------------------------------
             * 2. Resolve destination: explicit -> snapshot on cart
             * ---------------------------------------------------------
             */

            $address = $this->resolveCartShippingAddress(
                $cart,
                $customerAddressUuid,
                $inlineAddress,
                true
            );

            /*
             * ---------------------------------------------------------
             * 3. Live cart amounts (never trust frontend values)
             * ---------------------------------------------------------
             */

            [$subtotal, $totalWeight] = $this->cartAmounts($cart);

            /*
             * ---------------------------------------------------------
             * 4. Validate + price the selected rate server-side
             * ---------------------------------------------------------
             */

            $selected = $this->resolveSelectedRate($rateUuid, [
                'country_code' => (string) ($address['country_code'] ?? ''),
                'state_code' => (string) ($address['state_code'] ?? ''),
                'postal_code' => (string) ($address['postal_code'] ?? ''),
                'order_amount' => $subtotal,
                'weight' => $totalWeight,
            ]);

            /*
             * ---------------------------------------------------------
             * 5. Store selection + recalculate all cart prices
             * ---------------------------------------------------------
             */

            return $this->cartService()->setShipping($cart, [
                'shipping_rate_uuid' => $selected['uuid'],
                'shipping_method_uuid' => $selected['method']['uuid'],
                'shipping_method_name' => $selected['method']['name'],
                'shipping_method_code' => $selected['method']['code'],
                'shipping_amount' => $selected['amount'],
                'estimated_delivery_min_days' => $selected['estimated_days']['min'] ?? null,
                'estimated_delivery_max_days' => $selected['estimated_days']['max'] ?? null,
                'shipping_address' => $address,
            ]);
        });
    }

    /**
     * Resolve the shipping destination for a cart operation.
     *
     * Priority: saved customer address -> explicit inline address ->
     * destination snapshotted on the cart from an earlier step.
     *
     * @param \App\Modules\Cart\Models\Cart $cart
     * @param string|null $customerAddressUuid
     * @param array<string, mixed> $inlineAddress
     * @param bool $allowSnapshot Allow falling back to the cart's stored address.
     * @return array<string, mixed>
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \RuntimeException
     */
    private function resolveCartShippingAddress(
        Cart $cart,
        ?string $customerAddressUuid = null,
        array $inlineAddress = [],
        bool $allowSnapshot = false
    ): array {

        /*
         * Saved customer address (authenticated flow).
         */
        if (!empty($customerAddressUuid)) {
            $addressQuery = CustomerAddress::query()
                ->where('uuid', $customerAddressUuid)
                ->where('is_active', true);

            if ($cart->customer_id !== null) {
                $addressQuery->where('customer_id', $cart->customer_id);
            }

            $address = $addressQuery->first();

            if (!$address) {
                throw (new ModelNotFoundException())
                    ->setModel(CustomerAddress::class, [$customerAddressUuid]);
            }

            return [
                'uuid' => $address->uuid,
                'label' => $address->label,
                'city' => $address->city,
                'state' => $address->state,
                'state_code' => strtoupper((string) $address->state_code),
                'postal_code' => (string) $address->postal_code,
                'country' => $address->country,
                'country_code' => strtoupper((string) $address->country_code),
            ];
        }

        /*
         * Inline destination (guest checkout).
         */
        if (
            !empty($inlineAddress['country_code'])
            || !empty($inlineAddress['country'])
            || !empty($inlineAddress['postal_code'])
        ) {
            return $this->normalizeAddress($inlineAddress);
        }

        /*
         * Destination captured in an earlier step (rates lookup).
         */
        if ($allowSnapshot && !empty($cart->shipping_address)) {
            return $this->normalizeAddress(
                is_array($cart->shipping_address) ? $cart->shipping_address : []
            );
        }

        throw new RuntimeException(
            'A shipping address is required to calculate shipping.'
        );
    }

    /**
     * Normalize any address payload into canonical destination keys.
     *
     * Accepts both country/country_code and state/state_code spellings.
     *
     * @param array<string, mixed> $address
     * @return array<string, string>
     */
    private function normalizeAddress(array $address): array
    {
        return [
            'country_code' => strtoupper(trim((string) (
                $address['country_code'] ?? $address['country'] ?? ''
            ))),
            'state_code' => strtoupper(trim((string) (
                $address['state_code'] ?? $address['state'] ?? ''
            ))),
            'postal_code' => trim((string) ($address['postal_code'] ?? '')),
        ];
    }

    /**
     * Ensure the caller owns the given cart.
     *
     * @param \App\Modules\Cart\Models\Cart $cart
     * @param int|null $customerId
     * @param string|null $guestToken
     * @return void
     */
    private function authorizeCart(
        Cart $cart,
        ?int $customerId = null,
        ?string $guestToken = null
    ): void {
        if ($customerId !== null) {
            if ((int) $cart->customer_id !== (int) $customerId) {
                abort(403, 'You are not authorized to access this cart.');
            }

            return;
        }

        if (
            $guestToken === null
            || $cart->guest_token === null
            || !hash_equals($cart->guest_token, $guestToken)
        ) {
            abort(403, 'You are not authorized to access this cart.');
        }
    }

    /**
     * Live subtotal and weight of the cart.
     *
     * Variant price/weight take priority over product values.
     *
     * @param \App\Modules\Cart\Models\Cart $cart
     * @return array{0: float, 1: float} [subtotal, totalWeight]
     */
    private function cartAmounts(Cart $cart): array
    {
        $subtotal = 0.0;
        $totalWeight = 0.0;

        foreach ($cart->items as $item) {
            $unitPrice = (float) ($item->variant?->price ?? $item->product?->price ?? 0);
            $quantity = (float) $item->quantity;

            $subtotal += $unitPrice * $quantity;

            $weight = (float) ($item->variant?->weight ?? $item->product?->weight ?? 0);

            $totalWeight += $weight * $quantity;
        }

        return [$subtotal, $totalWeight];
    }

    /**
     * Lazily resolve the cart service (avoids constructor coupling).
     *
     * @return \App\Modules\Cart\Services\CartService
     */
    private function cartService(): \App\Modules\Cart\Services\CartService
    {
        return app(\App\Modules\Cart\Services\CartService::class);
    }

    /**
     * Resolve and validate a selected shipping rate.
     *
     * IMPORTANT:
     * Frontend only sends the selected rate UUID.
     * Shipping amount is ALWAYS calculated server-side.
     *
     * @param string $rateUuid
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     *
     * @throws \RuntimeException
     */
    public function resolveSelectedRate(
        string $rateUuid,
        array $context
    ): array {
        $rate = $this->findApplicableRate(
            $rateUuid,
            $context
        );

        if (!$rate) {
            throw new \RuntimeException(
                'Selected shipping method is no longer available.'
            );
        }

        $orderAmount = (float) ($context['order_amount'] ?? 0);

        $weight = (float) ($context['weight'] ?? 0);

        /*
        * IMPORTANT:
        *
        * Never trust shipping amount sent by frontend.
        *
        * Calculate again from database configuration.
        */
        $shippingAmount = $this->calculateRate(
            $rate,
            $orderAmount,
            $weight
        );

        $method = $rate->method;

        return [
            'uuid' => $rate->uuid,

            'method' => [
                'uuid' => $method->uuid,
                'name' => $method->name,
                'code' => $method->code,
            ],

            'amount' => round($shippingAmount, 2),

            'currency' => 'INR',

            'estimated_days' => [
                'min' => $method->min_delivery_days,
                'max' => $method->max_delivery_days,
            ],

            'is_free' => $shippingAmount <= 0,
        ];
    }

    /**
     * Find a shipping rate that is currently applicable.
     *
     * Validates:
     *
     * - Rate UUID
     * - Rate active status
     * - Shipping zone
     * - Country
     * - State
     * - Postal code
     * - Order amount
     * - Weight
     *
     * @param string $rateUuid
     * @param array<string, mixed> $context
     * @return ShippingRate|null
     */
    private function findApplicableRate(
        string $rateUuid,
        array $context
    ): ?ShippingRate {
        $countryCode = strtoupper(
            trim((string) ($context['country_code'] ?? ''))
        );

        $stateCode = strtoupper(
            trim((string) ($context['state_code'] ?? ''))
        );

        $postalCode = trim(
            (string) ($context['postal_code'] ?? '')
        );

        $orderAmount = (float) (
            $context['order_amount'] ?? 0
        );

        $weight = (float) (
            $context['weight'] ?? 0
        );

        /*
        * ---------------------------------------------------------
        * 1. Find selected active rate
        * ---------------------------------------------------------
        */

        $rate = ShippingRate::query()
            ->with('method')
            ->where('uuid', $rateUuid)
            ->where('is_active', true)
            ->first();

        if (!$rate) {
            return null;
        }

        /*
        * ---------------------------------------------------------
        * 2. Validate shipping method
        * ---------------------------------------------------------
        */

        if (!$rate->method) {
            return null;
        }

        if (!$rate->method->is_active) {
            return null;
        }

        /*
        * ---------------------------------------------------------
        * 3. Find rate's shipping zone
        * ---------------------------------------------------------
        */

        $zone = ShippingZone::query()
            ->where('id', $rate->shipping_zone_id)
            ->where('is_active', true)
            ->first();

        if (!$zone) {
            return null;
        }

        /*
        * ---------------------------------------------------------
        * 4. Validate destination against zone
        * ---------------------------------------------------------
        */

        if (!$this->zoneMatches(
            $zone,
            $countryCode,
            $stateCode,
            $postalCode
        )) {
            return null;
        }

        /*
        * ---------------------------------------------------------
        * 5. Validate weight range
        * ---------------------------------------------------------
        */

        if (
            $rate->min_weight !== null &&
            $weight < (float) $rate->min_weight
        ) {
            return null;
        }

        if (
            $rate->max_weight !== null &&
            $weight > (float) $rate->max_weight
        ) {
            return null;
        }

        /*
        * ---------------------------------------------------------
        * 6. Validate order amount range
        * ---------------------------------------------------------
        */

        if (
            $rate->min_order_amount !== null &&
            $orderAmount < (float) $rate->min_order_amount
        ) {
            return null;
        }

        if (
            $rate->max_order_amount !== null &&
            $orderAmount > (float) $rate->max_order_amount
        ) {
            return null;
        }

        /*
        * ---------------------------------------------------------
        * 7. Everything is valid.
        * ---------------------------------------------------------
        */

        return $rate;
    }


}
