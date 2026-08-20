<?php

declare(strict_types=1);

namespace App\Modules\Media\Actions;

use App\Modules\Media\Services\MediaService;

class SetPrimaryMediaAction
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    public function execute(string $uuid)
    {
        return $this->mediaService->setPrimary($uuid);
    }
}