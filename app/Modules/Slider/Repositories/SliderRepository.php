<?php

declare(strict_types=1);

namespace App\Modules\Slider\Repositories;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Models\SliderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Repository responsible for slider data access operations.
 *
 * Handles database interactions for sliders and their items
 * including listing, filtering, creating, updating, deleting,
 * restoring, and status changes.
 *
 * @package App\Modules\Slider\Repositories
 * @author Sanjiv Kumar Kushwaha
 */
class SliderRepository
{
    /**
     * Retrieve paginated sliders with optional filters.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Slider::query()
            ->with(['items' => function ($query) {
                $query->with('media')->orderBy('sort_order');
            }]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('placement', 'like', "%{$search}%");
            });
        }

        $filterValues = $filters['filters'] ?? [];

        if (
            isset($filterValues['name'])
            && $filterValues['name'] !== ''
        ) {
            $query->where('name', 'like', '%' . $filterValues['name'] . '%');
        }

        if (
            isset($filterValues['code'])
            && $filterValues['code'] !== ''
        ) {
            $query->where('code', 'like', '%' . $filterValues['code'] . '%');
        }

        if (
            isset($filterValues['placement'])
            && $filterValues['placement'] !== ''
        ) {
            $query->where('placement', 'like', '%' . $filterValues['placement'] . '%');
        }

        if (
            isset($filterValues['status'])
            && $filterValues['status'] !== ''
        ) {
            $query->where('is_active', (bool) $filterValues['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $allowedSorts = ['name', 'code', 'placement', 'is_active', 'created_at', 'updated_at'];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        return $query->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Find a slider by database ID.
     *
     * @param int $id The slider ID.
     * @return Slider|null
     */
    public function findById(int $id): ?Slider
    {
        return Slider::find($id);
    }

    /**
     * Find a slider by UUID.
     *
     * @param string $uuid The slider UUID.
     * @return Slider|null
     */
    public function findByUuid(string $uuid): ?Slider
    {
        return Slider::with(['items' => function ($query) {
            $query->with('media')->orderBy('sort_order');
        }])->where('uuid', $uuid)->first();
    }

    /**
     * Find a slider by UUID or throw an exception.
     *
     * @param string $uuid The slider UUID.
     * @return Slider
     */
    public function findByUuidOrFail(string $uuid): Slider
    {
        return Slider::with(['items' => function ($query) {
            $query->with('media')->orderBy('sort_order');
        }])->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Find an active slider by code including only active items
     * that fall within their scheduling window. Used by the storefront.
     *
     * @param string $code The slider code - Example: home_hero.
     * @return Slider|null
     */
    public function findActiveByCode(string $code): ?Slider
    {
        return Slider::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->with(['activeItems' => function ($query) {
                $query->with('media')->orderBy('sort_order');
            }])
            ->first();
    }

    /**
     * Find a slider by UUID including trashed records.
     *
     * @param string $uuid The slider UUID.
     * @return Slider|null
     */
    public function findByUuidWithTrashed(string $uuid): ?Slider
    {
        return Slider::withTrashed()
            ->with(['items' => function ($query) {
                $query->with('media')->orderBy('sort_order');
            }])
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Create a new slider.
     *
     * @param array<string, mixed> $data The slider data.
     * @return Slider
     */
    public function create(array $data): Slider
    {
        return Slider::create($data);
    }

    /**
     * Update an existing slider.
     *
     * @param Slider $slider The slider instance.
     * @param array<string, mixed> $data The updated slider data.
     * @return Slider
     */
    public function update(Slider $slider, array $data): Slider
    {
        $slider->update($data);

        return $slider->fresh(['items' => function ($query) {
            $query->with('media')->orderBy('sort_order');
        }]);
    }

    /**
     * Change the active status of a slider.
     *
     * @param Slider $slider The slider instance.
     * @param bool $status The new status value.
     * @return Slider
     */
    public function changeStatus(Slider $slider, bool $status): Slider
    {
        $slider->update(['is_active' => $status]);

        return $slider->fresh(['items' => function ($query) {
            $query->with('media')->orderBy('sort_order');
        }]);
    }

    /**
     * Soft delete a slider.
     *
     * @param Slider $slider The slider instance.
     * @return bool
     */
    public function delete(Slider $slider): bool
    {
        return (bool) $slider->delete();
    }

    /**
     * Restore a soft-deleted slider.
     *
     * @param string $uuid The slider UUID.
     * @return Slider
     */
    public function restore(string $uuid): Slider
    {
        $slider = Slider::withTrashed()->where('uuid', $uuid)->firstOrFail();

        $slider->restore();

        return $slider->refresh();
    }

    /**
     * Create a new slider item.
     *
     * @param array<string, mixed> $data The slider item data.
     * @return SliderItem
     */
    public function createItem(array $data): SliderItem
    {
        return SliderItem::create($data);
    }

    /**
     * Find a slider item by UUID belonging to a given slider.
     *
     * @param Slider $slider The parent slider.
     * @param string $uuid The slider item UUID.
     * @return SliderItem|null
     */
    public function findItemByUuid(Slider $slider, string $uuid): ?SliderItem
    {
        return SliderItem::with('media')
            ->where('slider_id', $slider->id)
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Update an existing slider item.
     *
     * @param SliderItem $sliderItem The slider item instance.
     * @param array<string, mixed> $data The updated data.
     * @return SliderItem
     */
    public function updateItem(SliderItem $sliderItem, array $data): SliderItem
    {
        $sliderItem->update($data);

        return $sliderItem->fresh('media');
    }

    /**
     * Delete a slider item.
     *
     * @param SliderItem $sliderItem The slider item instance.
     * @return bool
     */
    public function deleteItem(SliderItem $sliderItem): bool
    {
        return (bool) $sliderItem->delete();
    }
}

