<?php

declare(strict_types=1);

namespace App\Modules\Media\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Media\Actions\DeleteMediaAction;
use App\Modules\Media\Actions\ForceDeleteMediaAction;
use App\Modules\Media\Actions\ListMediaAction;
use App\Modules\Media\Actions\RestoreMediaAction;
use App\Modules\Media\Actions\ShowMediaAction;
use App\Modules\Media\Actions\UpdateMediaAction;
use App\Modules\Media\Actions\UploadMediaAction;
use App\Modules\Media\Requests\MediaListRequest;
use App\Modules\Media\Requests\UpdateMediaRequest;
use App\Modules\Media\Requests\UploadMediaRequest;
use App\Modules\Media\Resources\MediaResource;
use App\Modules\Media\Actions\SetPrimaryMediaAction;

class MediaController extends Controller
{
    public function __construct(
        private readonly UploadMediaAction $uploadMediaAction,
        private readonly ListMediaAction $listMediaAction,
        private readonly ShowMediaAction $showMediaAction,
        private readonly UpdateMediaAction $updateMediaAction,
        private readonly DeleteMediaAction $deleteMediaAction,
        private readonly RestoreMediaAction $restoreMediaAction,
        private readonly ForceDeleteMediaAction $forceDeleteMediaAction,
        private readonly SetPrimaryMediaAction $setPrimaryMediaAction
    ) {
    }

    /**
     * GET /media
     */
    public function index(MediaListRequest $request)
    {
        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $media */
        $media = $this->listMediaAction->execute($request->validated());

        return ApiResponse::paginated(
            $media,
            MediaResource::collection($media),
            "Media fetched successfully."
        );
    }

    /**
     * GET /media/{uuid}
     */
    public function show(string $uuid)
    {
        $media = $this->showMediaAction->execute($uuid);

        return ApiResponse::success(
            new MediaResource($media),
            "Media fetched successfully."
        );
    }

    /**
     * POST /media
     */
    public function store(UploadMediaRequest $request)
    {
        $media = $this->uploadMediaAction->execute($request->validated());

        return ApiResponse::success(
            new MediaResource($media),
            "Media uploaded successfully."
        );
    }

    /**
     * PUT /media/{uuid}
     */
    public function update(UpdateMediaRequest $request, string $uuid)
    {
        $media = $this->updateMediaAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new MediaResource($media),
            "Media updated successfully."
        );
    }

    /**
     * DELETE /media/{uuid}
     */
    public function destroy(string $uuid)
    {
        $this->deleteMediaAction->execute($uuid);

        return ApiResponse::success([], "Media deleted successfully.");
    }

    /**
     * POST /media/{uuid}/restore
     */
    public function restore(string $uuid)
    {
        $media = $this->restoreMediaAction->execute($uuid);

        return ApiResponse::success(
            new MediaResource($media),
            "Media restored successfully."
        );
    }

    /**
     * DELETE /media/{uuid}/force
     */
    public function forceDelete(string $uuid)
    {
        $this->forceDeleteMediaAction->execute($uuid);

        return ApiResponse::success(
            [],
            "Media permanently deleted successfully."
        );
    }

    /**
     * PATCH /media/{uuid}/primary
     */
    public function setPrimary(string $uuid)
    {
        $media = $this->setPrimaryMediaAction
            ->execute($uuid);

        return ApiResponse::success(
            new MediaResource($media),
            'Media primary status updated successfully.'
        );
    }
}
