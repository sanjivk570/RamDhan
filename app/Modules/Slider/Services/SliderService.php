<?php

declare(strict_types=1);

namespace App\Modules\Slider\Services;

use App\Modules\Slider\Models\Slider;
use App\Modules\Slider\Models\SliderItem;
use App\Modules\Slider\Repositories\SliderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service responsible for slider-related business logic.
 *
 * Handles slider and slider-item operations including listing,
 * creating, updating, status changes, deleting, restoring,
 * and storefront (public) retrieval. Delegates database
 * operations to the slider repository.
 *
 * @package App\Modules\Slider\Services
 * @author Sanjiv Kumar Kushwaha
 */
class SliderService
{
    /**
     * Create a new service instance.
     *
     * @param SliderRepository $sliderRepository The slider repository.
     */
    public function __construct(
        private readonly SliderRepository $sliderRepository
    ) {
    }

    /**
     * Retrieve a paginated list of sliders.
     *
     * @param array<string, mixed> $filters The filter criteria.
     * @return LengthAwarePaginator
     */
    public function list(array $filters): LengthAwarePaginator
    {
        return $this->sliderRepository->paginate($filters);
    }

    /**
     * Retrieve slider details by UUID.
     *
     * @param string $uuid The slider UUID.
     * @return Slider
     */
    public function details(string $uuid): Slider
    {
        return $this->sliderRepository->findByUuidOrFail($uuid);
    }

    /**
     * Create a new slider.
     *
     * @param array<string, mixed> $data The slider data.
     * @return Slider
     */
    public function create(array $data): Slider
    {
        return $this->sliderRepository->create($data);
    }

    /**
     * Update an existing slider.
     *
     * @param string $uuid The slider UUID.
     * @param array<string, mixed> $data The updated slider data.
     * @return Slider
     */
    public function update(string $uuid, array $data): Slider
    {
        $slider = $this->sliderRepository->findByUuidOrFail($uuid);

        return $this->sliderRepository->update($slider, $data);
    }

    /**
     * Change the active status of a slider.
     *
     * @param string $uuid The slider UUID.
     * @param bool $status The new status value.
     * @return Slider
     */
    public function changeStatus(string $uuid, bool $status): Slider
    {
        $slider = $this->sliderRepository->findByUuidOrFail($uuid);

        return $this->sliderRepository->changeStatus($slider, $status);
    }

    /**
     * Delete a slider.
     *
     * Performs a soft delete on the specified slider.
     *
     * @param string $uuid The slider UUID.
     * @return void
     */
    public function delete(string $uuid): void
    {
        $slider = $this->sliderRepository->findByUuidOrFail($uuid);

        $this->sliderRepository->delete($slider);
    }

    /**
     * Restore a soft-deleted slider.
     *
     * @param string $uuid The slider UUID.
     * @return Slider
     */
    public function restore(string $uuid): Slider
    {
        return $this->sliderRepository->restore($uuid);
    }

    /**
     * Retrieve an active slider for the storefront by code.
     *
     * Returns null when the slider is inactive or not found so
     * the controller can respond with a 404.
     *
     * @param string $code The slider code - Example: home_hero.
     * @return Slider|null
     */
    public function storefrontDetails(string $code): ?Slider
    {
        return $this->sliderRepository->findActiveByCode($code);
    }

    /**
     * Create a new slider item (slide) for a given slider.
     *
     * @param string $sliderUuid The parent slider UUID.
     * @param array<string, mixed> $data The slider item data.
     * @return SliderItem
     */
    public function createItem(string $sliderUuid, array $data): SliderItem
    {
        $slider = $this->sliderRepository->findByUuidOrFail($sliderUuid);

        $data['slider_id'] = $slider->id;

        return $this->sliderRepository->createItem($data);
    }

    /**
     * Update an existing slider item.
     *
     * @param string $sliderUuid The parent slider UUID.
     * @param string $itemUuid The slider item UUID.
     * @param array<string, mixed> $data The updated data.
     * @return SliderItem
     */
    public function updateItem(string $sliderUuid, string $itemUuid, array $data): SliderItem
    {
        $slider = $this->sliderRepository->findByUuidOrFail($sliderUuid);

        $sliderItem = $this->sliderRepository->findItemByUuid($slider, $itemUuid);

        if (!$sliderItem) {
            throw new \RuntimeException('Slider item not found.');
        }

        return $this->sliderRepository->updateItem($sliderItem, $data);
    }

    /**
     * Delete a slider item.
     *
     * @param string $sliderUuid The parent slider UUID.
     * @param string $itemUuid The slider item UUID.
     * @return void
     */
    public function deleteItem(string $sliderUuid, string $itemUuid): void
    {
        $slider = $this->sliderRepository->findByUuidOrFail($sliderUuid);

        $sliderItem = $this->sliderRepository->findItemByUuid($slider, $itemUuid);

        if (!$sliderItem) {
            throw new \RuntimeException('Slider item not found.');
        }

        $this->sliderRepository->deleteItem($sliderItem);
    }
}
