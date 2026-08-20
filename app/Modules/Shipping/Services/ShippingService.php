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

        $zone = $zones->first(function (ShippingZone $zone) use (
            $countryCode,
            $stateCode,
            $postalCode
        ) {
            return $this->zoneMatches(
                $zone,
                $countryCode,
                $stateCode,
                $postalCode
            );
        });

        if (!$zone) {
            return collect();
        }

        $rates = ShippingRate::query()
            ->with("method")
            ->where("shipping_zone_id", $zone->id)
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
     * Calculate shipping from actual cart + saved customer address.
     *
     * Frontend does not send:
     * - country
     * - state
     * - postal code
     * - order amount
     * - weight
     *
     * Backend calculates everything.
     */
    public function calculateCartShipping(
        string $cartUuid,
        string $customerAddressUuid,
        ?int $customerId = null
    ): array {
        return DB::transaction(function () use (
            $cartUuid,
            $customerAddressUuid,
            $customerId
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
             * 2. Security:
             * Customer can only calculate shipping for own cart.
             * ---------------------------------------------------------
             */

            if ($customerId !== null) {

                if ((int) $cart->customer_id !== (int) $customerId) {
                    abort(
                        403,
                        'You are not authorized to access this cart.'
                    );
                }
            }

            /*
             * ---------------------------------------------------------
             * 3. Load customer's saved address
             * ---------------------------------------------------------
             */

            $addressQuery = CustomerAddress::query()
                ->where('uuid', $customerAddressUuid)
                ->where('is_active', true);

            if ($customerId !== null) {
                $addressQuery->where(
                    'customer_id',
                    $customerId
                );
            } else {
                $addressQuery->where(
                    'customer_id',
                    $cart->customer_id
                );
            }

            $address = $addressQuery->first();

            if (!$address) {
                throw (new ModelNotFoundException())
                    ->setModel(
                        CustomerAddress::class,
                        [$customerAddressUuid]
                    );
            }

            /*
             * ---------------------------------------------------------
             * 4. Validate cart items
             * ---------------------------------------------------------
             */

            if ($cart->items->isEmpty()) {
                return [
                    'cart_uuid' => $cart->uuid,
                    'customer_address_uuid' => $address->uuid,
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
             * 5. Calculate actual cart amount + weight
             * ---------------------------------------------------------
             */

            $subtotal = 0.0;
            $totalWeight = 0.0;

            foreach ($cart->items as $item) {

                $product = $item->product;
                $variant = $item->variant;

                /*
                 * Variant price has priority.
                 */
                $unitPrice = $variant?->price
                    ?? $product?->price
                    ?? 0;

                $quantity = (int) $item->quantity;

                $lineTotal = (float) $unitPrice * $quantity;

                $subtotal += $lineTotal;

                /*
                 * Weight should preferably be available
                 * on product/variant.
                 *
                 * If current schema doesn't contain weight,
                 * this remains 0 until weight is added.
                 */
                $weight = (float) (
                    $variant?->weight
                    ?? $product?->weight
                    ?? 0
                );

                $totalWeight += $weight * $quantity;
            }

            /*
             * ---------------------------------------------------------
             * 6. Existing shipping rate calculation
             * ---------------------------------------------------------
             */

            $rates = $this->calculateRates([
                'country_code' => $address->country_code,
                'state_code' => $address->state_code,
                'postal_code' => $address->postal_code,
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

                'customer_address_uuid' => $address->uuid,

                'address' => [
                    'uuid' => $address->uuid,
                    'type' => $address->type,
                    'label' => $address->label,
                    'city' => $address->city,
                    'state' => $address->state,
                    'state_code' => $address->state_code,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'country_code' => $address->country_code,
                ],

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
