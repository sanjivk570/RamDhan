<?php

declare(strict_types=1);

namespace App\Modules\User\Actions;

use App\Modules\Shipping\Services\ShippingService;

/**
 * Handle the user listing action.
 *
 * This action delegates the process of retrieving
 * users based on the provided filters to the
 * user service.
 *
 * @package App\Modules\User\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ListShippingMethodAction
{
    /**
     * Create a new action instance.
     *
     * @param ShippingService $shippingService The user service.
     */
    public function __construct(
        private readonly ShippingService $shippingService
    ) {
    }

    /**
     * Execute the user listing action.
     *
     * Retrieves a paginated list of users based on
     * the supplied filter criteria.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        return $this->shippingService->listMethods($filters);
    }
}