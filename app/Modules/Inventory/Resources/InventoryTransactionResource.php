<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,

            "type" => $this->type,

            "quantity" => (float) $this->quantity,

            "quantity_before" => (float) $this->quantity_before,

            "quantity_after" => (float) $this->quantity_after,

            "reference" => [
                "type" => $this->reference_type,
                "id" => $this->reference_id,
            ],

            "notes" => $this->notes,

            "created_by" => $this->whenLoaded("createdBy", function () {
                if (!$this->createdBy) {
                    return null;
                }

                return [
                    "uuid" => $this->createdBy->uuid,
                    "name" => trim(
                        $this->createdBy->first_name .
                            " " .
                            $this->createdBy->last_name
                    ),
                ];
            }),

            "created_at" => $this->created_at?->toISOString(),
        ];
    }
}
