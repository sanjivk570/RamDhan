<?php

declare(strict_types=1);

namespace App\Modules\Media\Actions;

use App\Modules\Media\Services\MediaService;

class UploadMediaAction
{
    public function __construct(private readonly MediaService $mediaService)
    {
    }

    public function execute(array $data)
    {
        return $this->mediaService->upload($data);
    }
}
