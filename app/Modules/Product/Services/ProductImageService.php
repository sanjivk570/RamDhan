<?php

declare(strict_types=1);

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\ProductImage;
use App\Modules\Product\Repositories\ProductImageRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Service responsible for product image operations.
 *
 * Handles product image creation, deletion, primary image
 * management, file storage, and permanent deletion.
 *
 * @package App\Modules\Product\Services
 * @author Sanjiv Kumar Kushwaha
 */
class ProductImageService
{

    /**
     * Create a new product image service.
     *
     * @param ProductImageRepository $productImageRepository
     */
    public function __construct(
        private readonly ProductImageRepository $productImageRepository
    ) {
    }

    /**
     * Create a product image.
     *
     * Uploads the image, stores the image record, and handles
     * primary image assignment.
     *
     * @param string $productUuid
     * @param array<string, mixed> $data
     * @return ProductImage
     *
     * @throws RuntimeException
     */
    public function create(string $productUuid, array $data): ProductImage
    {
        $product = $this->productImageRepository->findProductByUuid(
            $productUuid
        );

        if (!$product) {
            throw new RuntimeException("Product not found.");
        }

        /** @var UploadedFile $image */
        $image = $data["image"];

        $isPrimary = (bool) ($data["is_primary"] ?? false);

        $sortOrder = (int) ($data["sort_order"] ?? 0);

        $path = $image->store("products/" . $product->slug, "public");

        $imageUrl = Storage::url($path);

        try {
            $productImage = DB::transaction(function () use ($product, $data, $path, $imageUrl, $isPrimary, $sortOrder) {
                /*
                 * If this image is primary,
                 * remove primary from existing images.
                 */
                if ($isPrimary) {
                    $this->productImageRepository->clearPrimary($product->id);
                }

                return $this->productImageRepository->create([
                    "uuid" => (string) Str::uuid(),

                    "product_id" => $product->id,

                    "image_path" => $path,

                    "image_url" => $imageUrl,

                    "alt_text" => $data["alt_text"] ?? null,

                    "sort_order" => $sortOrder,

                    "is_primary" => $isPrimary,
                ]);
            });
        } catch (\Throwable $exception) {
            /*
             * Remove uploaded file if database
             * operation fails.
             */
            Storage::disk("public")->delete($path);

            throw $exception;
        }

        /*
         * If this is the first image and caller
         * did not explicitly make it primary,
         * make it primary automatically.
         */
        if (!$isPrimary) {
            $hasPrimary = ProductImage::where("product_id", $product->id)->where("is_primary", true)->exists();
            if (!$hasPrimary) {
                DB::transaction(function () use ($product, $productImage) {
                    $this->productImageRepository->clearPrimary($product->id);

                    $this->productImageRepository->setPrimary($productImage);
                });
            }
        }
        return $productImage->refresh();
    }

    /**
     * Soft delete a product image.
     *
     * If the deleted image is primary, the next available
     * image is automatically assigned as primary.
     *
     * @param string $productUuid
     * @param string $imageUuid
     * @return void
     *
     * @throws RuntimeException
     */
    public function delete(string $productUuid, string $imageUuid): void
    {
        $product = $this->productImageRepository->findProductByUuid(
            $productUuid
        );

        if (!$product) {
            throw new RuntimeException("Product not found.");
        }

        $image = $this->productImageRepository->findByUuidAndProduct(
            $imageUuid,
            $product->id
        );

        if (!$image) {
            throw new RuntimeException("Product image not found.");
        }

        $wasPrimary = $image->is_primary;
        $imagePath = $image->image_path;

        DB::transaction(function () use ($image, $product, $wasPrimary) {
            $this->productImageRepository->delete($image);

            /*
             * If primary image was deleted,
             * automatically select the next image.
             */
            if ($wasPrimary) {
                $nextImage = $this->productImageRepository->getFirstImage(
                    $product->id
                );

                if ($nextImage) {
                    $this->productImageRepository->clearPrimary($product->id);

                    $this->productImageRepository->setPrimary($nextImage);
                }
            }
        });

        /*
         * Delete physical file after successful
         * database operation.
         */
        if ($imagePath) {
            Storage::disk("public")->delete($imagePath);
        }
    }

    /**
     * Set a product image as the primary image.
     *
     * @param string $productUuid
     * @param string $imageUuid
     * @return ProductImage
     *
     * @throws RuntimeException
     */
    public function setPrimary(
        string $productUuid,
        string $imageUuid
    ): ProductImage {
        $product = $this->productImageRepository->findProductByUuid(
            $productUuid
        );

        if (!$product) {
            throw new RuntimeException("Product not found.");
        }

        $image = $this->productImageRepository->findByUuidAndProduct(
            $imageUuid,
            $product->id
        );

        if (!$image) {
            throw new RuntimeException("Product image not found.");
        }

        return DB::transaction(function () use ($product, $image) {
            $this->productImageRepository->clearPrimary($product->id);

            return $this->productImageRepository->setPrimary($image);
        });
    }

    /**
     * Permanently delete a product image.
     *
     * If the deleted image is primary, the next available
     * image is automatically assigned as primary.
     *
     * @param string $productUuid
     * @param string $imageUuid
     * @return bool
     *
     * @throws RuntimeException
     */
    public function forceDelete(string $productUuid, string $imageUuid): bool
    {
        $product = $this->productImageRepository->findProductByUuid(
            $productUuid
        );

        if (!$product) {
            throw new RuntimeException("Product not found.");
        }

        $image = $this->productImageRepository->findByUuidAndProduct(
            $imageUuid,
            $product->id
        );

        if (!$image) {
            throw new RuntimeException("Product image not found.");
        }

        $wasPrimary = $image->is_primary;

        $imagePath = $image->image_path;

        $deleted = DB::transaction(function () use (
            $image,
            $product,
            $wasPrimary
        ) {
            /*
             * Permanently delete image record.
             */
            $deleted = $this->productImageRepository->forceDelete($image);

            /*
             * If primary image was permanently deleted,
             * automatically select the next available image.
             */
            if ($deleted && $wasPrimary) {
                $nextImage = $this->productImageRepository->getFirstImage(
                    $product->id
                );

                if ($nextImage) {
                    $this->productImageRepository->clearPrimary($product->id);

                    $this->productImageRepository->setPrimary($nextImage);
                }
            }

            return $deleted;
        });

        /*
         * Delete physical file only after
         * successful database transaction.
         */
        if ($deleted && !empty($imagePath)) {
            Storage::disk("public")->delete($imagePath);
        }

        return $deleted;
    }
}
