<?php

declare(strict_types=1);

namespace App\Modules\Media\Actions;

use App\Modules\Media\Services\MediaService;

/**
 * Handle the media listing action.
 *
 * This action delegates the process of retrieving
 * media based on the provided filters to the
 * media service.
 *
 * @package App\Modules\Media\Actions
 * @author Sanjiv Kumar Kushwaha
 */
class ListMediaAction
{
    /**
     * Create a new action instance.
     *
     * @param MediaService $mediaService The media service.
     */
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    /**
     * Execute the media listing action.
     *
     * Retrieves a paginated list of media based on
     * the supplied filter criteria.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function execute(array $filters)
    {
        return $this->mediaService->list($filters);
    }
}