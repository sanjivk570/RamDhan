<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;
use App\Modules\Inventory\Actions\AdjustInventoryAction;
use App\Modules\Inventory\Actions\ListInventoryAction;
use App\Modules\Inventory\Actions\ListInventoryTransactionsAction;
use App\Modules\Inventory\Actions\ShowInventoryAction;
use App\Modules\Inventory\Actions\StockInAction;
use App\Modules\Inventory\Actions\StockOutAction;
use App\Modules\Inventory\Requests\AdjustInventoryRequest;
use App\Modules\Inventory\Requests\InventoryListRequest;
use App\Modules\Inventory\Requests\StockInRequest;
use App\Modules\Inventory\Requests\StockOutRequest;
use App\Modules\Inventory\Resources\InventoryStockResource;
use App\Modules\Inventory\Resources\InventoryTransactionResource;

class InventoryController extends Controller
{
    public function __construct(
        private readonly ListInventoryAction $listInventoryAction,
        private readonly ShowInventoryAction $showInventoryAction,
        private readonly StockInAction $stockInAction,
        private readonly StockOutAction $stockOutAction,
        private readonly AdjustInventoryAction $adjustInventoryAction,
        private readonly ListInventoryTransactionsAction $listInventoryTransactionsAction
    ) {}

    /**
     * List inventory.
     */
    public function index(InventoryListRequest $request)
    {
        $inventory = $this->listInventoryAction->execute($request->validated());

        return ApiResponse::paginated(
            $inventory,
            InventoryStockResource::collection($inventory),
            "Inventory fetched successfully."
        );
    }

    /**
     * Show inventory details.
     */
    public function show(string $uuid)
    {
        $inventory = $this->showInventoryAction->execute($uuid);

        return ApiResponse::success(
            new InventoryStockResource($inventory),
            "Inventory fetched successfully."
        );
    }

    /**
     * Add stock.
     */
    public function stockIn(StockInRequest $request, string $uuid)
    {
        $data = $request->validated();

        $inventory = $this->stockInAction->execute(
            $uuid,
            (float) $data["quantity"],
            $data["type"],
            $data["reference_type"] ?? null,
            $data["reference_id"] ?? null,
            $data["notes"] ?? null,
            Auth::id()
        );

        return ApiResponse::success(
            new InventoryStockResource($inventory),
            "Stock added successfully."
        );
    }

    /**
     * Remove stock.
     */
    public function stockOut(StockOutRequest $request, string $uuid)
    {
        $data = $request->validated();

        $inventory = $this->stockOutAction->execute(
            $uuid,
            (float) $data["quantity"],
            $data["type"],
            $data["reference_type"] ?? null,
            $data["reference_id"] ?? null,
            $data["notes"] ?? null,
            Auth::id()
        );

        return ApiResponse::success(
            new InventoryStockResource($inventory),
            "Stock removed successfully."
        );
    }

    /**
     * Adjust stock to an exact quantity.
     */
    public function adjust(AdjustInventoryRequest $request, string $uuid)
    {
        $data = $request->validated();

        $inventory = $this->adjustInventoryAction->execute(
            $uuid,
            (float) $data["quantity"],
            $data["notes"] ?? null,
            Auth::id()
        );

        return ApiResponse::success(
            new InventoryStockResource($inventory),
            "Inventory adjusted successfully."
        );
    }

    /**
     * Get inventory transaction history.
     */
    public function transactions(InventoryListRequest $request, string $uuid)
    {
        $perPage = (int) ($request->validated()["per_page"] ?? 20);

        $transactions = $this->listInventoryTransactionsAction->execute(
            $uuid,
            $perPage
        );

        return ApiResponse::paginated(
            $transactions,
            InventoryTransactionResource::collection($transactions),
            "Inventory transactions fetched successfully."
        );
    }
}
