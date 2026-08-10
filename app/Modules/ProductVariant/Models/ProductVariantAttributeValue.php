<?php

declare(strict_types=1);

namespace App\Modules\ProductVariant\Models;

use App\Modules\Attribute\Models\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantAttributeValue extends Model
{
    protected $table = "product_variant_attribute_values";

    protected $fillable = ["product_variant_id", "attribute_value_id"];

    /*
     * Variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, "product_variant_id");
    }

    /*
     * Selected attribute value.
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, "attribute_value_id");
    }
}
