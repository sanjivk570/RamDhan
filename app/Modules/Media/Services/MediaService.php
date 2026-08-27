<?php

declare(strict_types=1);

namespace App\Modules\Media\Services;

use App\Modules\Media\Models\Media;
use App\Modules\Media\Repositories\MediaRepository;
use App\Modules\Product\Models\Product;
use App\Modules\User\Models\User;
use App\Modules\Category\Models\Category;
use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Models\SliderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MediaService
{
    public function __construct(
        private readonly MediaRepository $mediaRepository
    ) {
    }

    /**
     * List media.
     */
    public function list(array $filters): LengthAwarePaginator
    {
        return $this->mediaRepository->paginate($filters);
    }

    /**
     * Show media.
     */
    public function details(string $uuid): Media
    {
        return $this->mediaRepository->findByUuidOrFail($uuid);
    }

    /**
     * Upload media.
     */
    public function upload(array $data): Media
    {
        $file = $data["file"];

        if (!$file instanceof UploadedFile) {
            throw new RuntimeException("Invalid uploaded file.");
        }

        $mediable = $this->resolveMediable(
            $data["mediable_type"],
            $data["mediable_uuid"]
        );

        $disk = $data['disk'] ?? 'public';

        $collection = $data['collection'] ?? 'default';

        $directory = "media/" . strtolower(class_basename($mediable)). '/' .
            $collection;

        $storedPath = $file->store($directory, $disk);

        if (!$storedPath) {
            throw new RuntimeException("Unable to store uploaded file.");
        }

        $type = $this->resolveMediaType($file->getMimeType());

        $isPrimary = (bool) ($data["is_primary"] ?? false);

        return DB::transaction(function () use (
            $file,
            $storedPath,
            $disk,
            $type,
            $isPrimary,
            $mediable,
            $data,
            $collection
        ) {
            if ($isPrimary) {
                $this->mediaRepository->clearPrimary(
                    $mediable->getMorphClass(),
                    $mediable->id,
                    $collection
                );
            }

            $media = $this->mediaRepository->create([
                "mediable_type" => $mediable->getMorphClass(),

                "mediable_id" => $mediable->id,

                "original_name" => $file->getClientOriginalName(),

                "file_name" => basename($storedPath),

                "disk" => $disk,

                'collection' => $collection,

                "path" => $storedPath,

                "mime_type" => $file->getMimeType(),

                "size" => $file->getSize(),

                "title" => $data["title"] ?? null,

                "alt_text" => $data["alt_text"] ?? null,

                "description" => $data["description"] ?? null,

                "type" => $type,

                "sort_order" => $data["sort_order"] ?? 0,

                "is_primary" => $isPrimary,
            ]);

            return $media;
        });
    }

    /**
     * Update media metadata.
     */
    public function update(string $uuid, array $data): Media
    {
        $media = $this->mediaRepository->findByUuidOrFail($uuid);

        $isPrimary = array_key_exists("is_primary", $data)
            ? (bool) $data["is_primary"]
            : $media->is_primary;

        return DB::transaction(function () use ($media, $data, $isPrimary) {
            if ($isPrimary && !$media->is_primary) {
                $this->mediaRepository->clearPrimary(
                    $media->mediable_type,
                    $media->mediable_id,
                    $media->collection
                );
            }

            $data["is_primary"] = $isPrimary;

            return $this->mediaRepository->update($media, $data);
        });
    }

    /**
     * Soft delete media.
     */
    public function delete(string $uuid): void
    {
        $media = $this->mediaRepository->findByUuidOrFail($uuid);

        $wasPrimary = $media->is_primary;

        $mediableType = $media->mediable_type;

        $mediableId = $media->mediable_id;

        DB::transaction(function () use (
            $media,
            $wasPrimary,
            $mediableType,
            $mediableId
        ) {
            $this->mediaRepository->delete($media);

            if ($wasPrimary) {
                $nextMedia = $this->mediaRepository->getFirstMedia(
                    $mediableType,
                    $mediableId,
                    $media->collection
                );

                if ($nextMedia) {
                    $this->mediaRepository->clearPrimary(
                        $mediableType,
                        $mediableId,
                        $media->collection
                    );

                    $this->mediaRepository->setPrimary($nextMedia);
                }
            }
        });
    }

    /**
     * Restore media.
     */
    public function restore(string $uuid): Media
    {
        $media = $this->mediaRepository->findWithTrashedByUuidOrFail($uuid);

        if (!$media->trashed()) {
            throw new RuntimeException("Media is already active.");
        }

        $this->mediaRepository->restore($media);

        return $media->refresh();
    }

    /**
     * Permanently delete media.
     */
    public function forceDelete(string $uuid): void
    {
        $media = $this->mediaRepository->findWithTrashedByUuidOrFail($uuid);

        $imagePath = $media->path;

        DB::transaction(function () use ($media) {
            $this->mediaRepository->forceDelete($media);
        });

        /*
         * Delete physical file only after
         * successful database transaction.
         */
        if (!empty($imagePath)) {
            Storage::disk($media->disk)->delete($imagePath);
        }
    }

    /**
     * Resolve owner model.
     */
    private function resolveMediable(string $type, string $uuid): object
    {
        $models = [
            "product" => Product::class,

            "category" => Category::class,

            //"brand" => Brand::class,

            "user" => User::class,

            "slider" => Slider::class,
            "slider_item" => SliderItem::class,
        ];

        if (!isset($models[$type])) {
            throw new RuntimeException("Invalid media owner type.");
        }

        $modelClass = $models[$type];

        $model = $modelClass
            ::query()
            ->where("uuid", $uuid)
            ->first();

        if (!$model) {
            throw new RuntimeException(ucfirst($type) . " not found.");
        }

        return $model;
    }

    /**
     * Resolve media type from MIME type.
     */
    private function resolveMediaType(?string $mimeType): string
    {
        if (!$mimeType) {
            return "other";
        }

        if (str_starts_with($mimeType, "image/")) {
            return "image";
        }

        if (str_starts_with($mimeType, "video/")) {
            return "video";
        }

        if (
            str_starts_with($mimeType, "application/pdf") ||
            str_starts_with($mimeType, "application/msword") ||
            str_starts_with($mimeType, "application/vnd.")
        ) {
            return "document";
        }

        return "other";
    }

    /**
     * Set media as primary.
     */
    public function setPrimary(string $uuid): Media
    {
        $media = $this->mediaRepository
            ->findByUuidOrFail($uuid);

        $this->mediaRepository
            ->clearPrimary(
                $media->mediable_type,
                $media->mediable_id,
                $media->collection
            );

        return $this->mediaRepository
            ->setPrimary($media);
    }

    public function deleteByMediable(
        string $mediableType,
        int $mediableId
    ): void {
        $this->mediaRepository->deleteByMediable(
            $mediableType,
            $mediableId
        );
    }

    public function restoreByMediable(
        string $mediableType,
        int $mediableId
    ): void {
        $this->mediaRepository->restoreByMediable(
            $mediableType,
            $mediableId
        );
    }

    /**
     * Permanently delete all media belonging to an owner.
     *
     * Physical files are deleted by the existing
     * individual forceDelete() method.
     */
    public function forceDeleteByMediable(
        string $mediableType,
        int $mediableId
    ): void {
        $mediaItems = $this->mediaRepository
            ->getAllByMediable(
                $mediableType,
                $mediableId
            );

        foreach ($mediaItems as $media) {
            $this->forceDelete(
                $media->uuid
            );
        }
    }
}
