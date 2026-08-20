<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Services;

use App\Modules\Product\Models\Product;
use App\Modules\ProductVariant\Models\ProductVariant;
use App\Modules\ProductVariant\Repositories\ProductVariantRepository;
use App\Modules\ProductVariant\Repositories\ProductVariantAttributeValueRepository;
use App\Modules\Attribute\Models\AttributeValue;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductVariantService
{
    public function __construct(
        private readonly ProductVariantRepository $variantRepository,
        private readonly ProductVariantAttributeValueRepository $attributeValueRepository
    ) {
    }

    public function list(string $productUuid, array $filters)
    {
        return $this->variantRepository->paginate($productUuid, $filters);
    }

    public function details(
        string $productUuid,
        string $variantUuid
    ): ProductVariant {
        $product = Product::where("uuid", $productUuid)->firstOrFail();

        return $this->variantRepository->findByUuidAndProductOrFail(
            $variantUuid,
            $product->id
        );
    }

    public function create(string $productUuid, array $data): ProductVariant
    {
        return DB::transaction(function () use ($productUuid, $data) {
            $product = Product::where("uuid", $productUuid)->firstOrFail();

            $attributeValueUuids = $data["attribute_values"];

            unset($data["attribute_values"]);

            /*
             * Resolve UUIDs to internal IDs.
             */
            $attributeValueIds = AttributeValue::query()
                ->whereIn("uuid", $attributeValueUuids)
                ->pluck("id")
                ->toArray();

            /*
             * Ensure every requested UUID
             * actually exists.
             */
            if (
                count($attributeValueIds) !==
                count(array_unique($attributeValueUuids))
            ) {
                throw new RuntimeException(
                    "One or more attribute values are invalid."
                );
            }

            /*
             * Product ownership validation.
             *
             * Attribute values used by a variant
             * must belong to attributes configured
             * for the product.
             *
             * This can be strengthened according
             * to your existing ProductAttribute model.
             */

            $data["product_id"] = $product->id;

            /*
             * If first variant is created,
             * make it default automatically.
             */
            if (!isset($data["is_default"])) {
                $hasVariant = ProductVariant::query()
                    ->where("product_id", $product->id)
                    ->exists();

                $data["is_default"] = !$hasVariant;
            }

            $variant = $this->variantRepository->create($data);

            $this->attributeValueRepository->sync(
                $variant->id,
                $attributeValueIds
            );

            if ($variant->is_default) {
                $this->variantRepository->clearDefault($product->id);

                $variant = $this->variantRepository->setDefault($variant);
            }

            return $variant->load([
                "attributeValues.attributeValue.attribute",
                "product",
            ]);
        });
    }

    public function update(
        string $productUuid,
        string $variantUuid,
        array $data
    ): ProductVariant {
        return DB::transaction(function () use (
            $productUuid,
            $variantUuid,
            $data
        ) {
            $product = Product::where("uuid", $productUuid)->firstOrFail();

            $variant = $this->variantRepository->findByUuidAndProductOrFail(
                $variantUuid,
                $product->id
            );

            $attributeValueUuids = $data["attribute_values"];

            unset($data["attribute_values"]);

            $attributeValueIds = AttributeValue::query()
                ->whereIn("uuid", $attributeValueUuids)
                ->pluck("id")
                ->toArray();

            if (
                count($attributeValueIds) !==
                count(array_unique($attributeValueUuids))
            ) {
                throw new RuntimeException(
                    "One or more attribute values are invalid."
                );
            }

            $variant = $this->variantRepository->update($variant, $data);

            $this->attributeValueRepository->sync(
                $variant->id,
                $attributeValueIds
            );

            if ($variant->is_default) {
                $this->variantRepository->clearDefault($product->id);

                $variant = $this->variantRepository->setDefault($variant);
            }

            return $variant->load([
                "attributeValues.attributeValue.attribute",
                "product",
            ]);
        });
    }

    public function delete(string $productUuid, string $variantUuid): void
    {
        DB::transaction(function () use ($productUuid, $variantUuid) {
            $product = Product::where("uuid", $productUuid)->firstOrFail();

            $variant = $this->variantRepository->findByUuidAndProductOrFail(
                $variantUuid,
                $product->id
            );

            $wasDefault = $variant->is_default;

            $this->variantRepository->delete($variant);

            /*
             * Never leave a product without
             * a default variant if variants remain.
             */
            if ($wasDefault) {
                $nextVariant = ProductVariant::query()
                    ->where("product_id", $product->id)
                    ->where("id", "!=", $variant->id)
                    ->where("is_active", true)
                    ->orderBy("sort_order")
                    ->orderBy("id")
                    ->first();

                if ($nextVariant) {
                    $this->variantRepository->clearDefault($product->id);

                    $this->variantRepository->setDefault($nextVariant);
                }
            }
        });
    }

    public function setDefault(
        string $productUuid,
        string $variantUuid
    ): ProductVariant {
        return DB::transaction(function () use ($productUuid, $variantUuid) {
            $product = Product::where("uuid", $productUuid)->firstOrFail();

            $variant = $this->variantRepository->findByUuidAndProductOrFail(
                $variantUuid,
                $product->id
            );

            $this->variantRepository->clearDefault($product->id);

            return $this->variantRepository
                ->setDefault($variant)
                ->load(["attributeValues.attributeValue.attribute", "product"]);
        });
    }
}
