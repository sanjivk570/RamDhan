<?php

declare(strict_types=1);

namespace App\Modules\Cart\Actions;

use App\Modules\Cart\Services\CartService;

/**
 * Application action for AdminListCartsAction.
 *
 * Keeps controllers thin and delegates business rules to the module service.
 */
final class AdminListCartsAction
{
    public function __construct(private readonly CartService $service) {}

    public function execute(array $filters)
    {
        return $this->service->listAdmin($filters);
    }
}
