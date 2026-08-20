<?php

declare(strict_types=1);

namespace App\Modules\Media\Actions;

use App\Modules\Media\Services\MediaService;

class ForceDeleteMediaAction
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    public function execute(string $uuid): void 
    {
        $this->mediaService->forceDelete($uuid);
    }
}