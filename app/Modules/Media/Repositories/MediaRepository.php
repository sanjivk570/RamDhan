<?php

declare(strict_types=1);

namespace App\Modules\Media\Repositories;

use App\Modules\Media\Models\Media;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MediaRepository
{
    /**
     * Retrieve paginated media.
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Media::query()
            ->with("mediable")

            /*
             * Global search.
             */
            ->when($filters["search"] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("original_name", "LIKE", "%{$search}%")
                        ->orWhere("file_name", "LIKE", "%{$search}%")
                        ->orWhere("title", "LIKE", "%{$search}%")
                        ->orWhere("mime_type", "LIKE", "%{$search}%");
                });
            })

            /*
             * Column filters.
             *
             * Same pattern as UserRepository:
             *
             * $filters['filters']['collection']
             */
            ->when(
                !empty($filters['filters']['collection']),
                function ($query) use ($filters) {

                    $query->where(
                        'collection',
                        $filters['filters']['collection']
                    );
                }
            )

            /*
             * Column filters.
             *
             * Same pattern as UserRepository:
             *
             * $filters['filters']['type']
             */
            ->when(!empty($filters["filters"]["original_name"]), function (
                $query
            ) use ($filters) {
                $query->where(
                    "original_name",
                    "LIKE",
                    "%" . $filters["filters"]["original_name"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["title"]), function ($query) use (
                $filters
            ) {
                $query->where(
                    "title",
                    "LIKE",
                    "%" . $filters["filters"]["title"] . "%"
                );
            })

            ->when(!empty($filters["filters"]["type"]), function ($query) use (
                $filters
            ) {
                $query->where("type", $filters["filters"]["type"]);
            })

            ->when(!empty($filters["filters"]["mime_type"]), function (
                $query
            ) use ($filters) {
                $query->where(
                    "mime_type",
                    "LIKE",
                    "%" . $filters["filters"]["mime_type"] . "%"
                );
            })

            ->when(
                isset($filters["filters"]["is_primary"]) &&
                    $filters["filters"]["is_primary"] !== "",
                function ($query) use ($filters) {
                    $query->where(
                        "is_primary",
                        (bool) $filters["filters"]["is_primary"]
                    );
                }
            )

            ->when(!empty($filters["filters"]["mediable_type"]), function (
                $query
            ) use ($filters) {
                $query->where(
                    "mediable_type",
                    $filters["filters"]["mediable_type"]
                );
            })

            ->orderBy(
                $filters["sort_by"] ?? "created_at",
                $filters["sort_order"] ?? "desc"
            )

            ->paginate($filters["per_page"] ?? 20);
    }

    /**
     * Find media by UUID.
     */
    public function findByUuid(string $uuid): ?Media
    {
        return Media::with("mediable")
            ->where("uuid", $uuid)
            ->first();
    }

    /**
     * Find media by UUID or fail.
     */
    public function findByUuidOrFail(string $uuid): Media
    {
        return Media::with("mediable")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    /**
     * Find media by UUID including soft deleted.
     */
    public function findWithTrashedByUuidOrFail(string $uuid): Media
    {
        return Media::withTrashed()
            ->with("mediable")
            ->where("uuid", $uuid)
            ->firstOrFail();
    }

    /**
     * Create media.
     */
    public function create(array $data): Media
    {
        return Media::create($data);
    }

    /**
     * Update media.
     */
    public function update(Media $media, array $data): Media
    {
        $media->update($data);

        return $media->refresh();
    }

    /**
     * Delete media.
     */
    public function delete(Media $media): bool
    {
        return (bool) $media->delete();
    }

    /**
     * Restore media.
     */
    public function restore(Media $media): bool
    {
        return (bool) $media->restore();
    }

    /**
     * Permanently delete media.
     */
    public function forceDelete(Media $media): bool
    {
        return (bool) $media->forceDelete();
    }

    /**
     * Get first media for owner.
     */
    public function getFirstMedia(string $mediableType, int $mediableId, string $collection = 'default'): ?Media
    {
        return Media::query()
            ->where("mediable_type", $mediableType)
            ->where("mediable_id", $mediableId)
            ->where('collection', $collection)
            ->whereNull('deleted_at')
            ->orderBy("sort_order")
            ->orderBy("id")
            ->first();
    }

    // /**
    //  * Clear primary media.
    //  */
    public function clearPrimary(string $mediableType, int $mediableId, string $collection = 'default')
    {
        return Media::query()
            ->where("mediable_type", $mediableType)
            ->where("mediable_id", $mediableId)
            ->where('collection', $collection)
            ->update([
                "is_primary" => false,
            ]);
    }

    /**
     * Set media as primary.
     */
    public function setPrimary(Media $media): Media
    {
        $media->update([
            "is_primary" => true,
        ]);

        return $media->refresh();
    }

    /**
     * Soft delete all media belonging to an owner.
     */
    public function deleteByMediable(string $mediableType, int $mediableId): void {
        Media::query()->where('mediable_type', $mediableType)
            ->where('mediable_id', $mediableId)
            ->delete();
    }

    /**
     * Restore all soft-deleted media belonging to an owner.
     */
    public function restoreByMediable(string $mediableType, int $mediableId): void 
    {
        Media::withTrashed()->where('mediable_type', $mediableType)
            ->where('mediable_id', $mediableId)
            ->restore();
    }

    /**
     * Get all media belonging to an owner,
     * including soft-deleted media.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Media>
     */
    public function getAllByMediable(string $mediableType, int $mediableId) {
        return Media::withTrashed()
            ->where(
                'mediable_type',
                $mediableType
            )
            ->where(
                'mediable_id',
                $mediableId
            )
            ->get();
    }
}
