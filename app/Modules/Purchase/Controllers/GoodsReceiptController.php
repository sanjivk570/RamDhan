<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Purchase\Actions\ListGoodsReceiptAction;
use App\Modules\Purchase\Actions\ShowGoodsReceiptAction;
use App\Modules\Purchase\Actions\CreateGoodsReceiptAction;
use App\Modules\Purchase\Actions\PostGoodsReceiptAction;
use App\Modules\Purchase\Actions\VoidGoodsReceiptAction;
use App\Modules\Purchase\Requests\GoodsReceiptListRequest;
use App\Modules\Purchase\Requests\CreateGoodsReceiptRequest;
use App\Modules\Purchase\Requests\ReceiptVoidRequest;
use App\Modules\Purchase\Resources\GoodsReceiptResource;
use Illuminate\Support\Facades\Auth;

final class GoodsReceiptController extends Controller
{
    public function __construct(
        private readonly ListGoodsReceiptAction $listAction,
        private readonly ShowGoodsReceiptAction $showAction,
        private readonly CreateGoodsReceiptAction $createAction,
        private readonly PostGoodsReceiptAction $postAction,
        private readonly VoidGoodsReceiptAction $voidAction
    ) {
    }

    public function index(GoodsReceiptListRequest $request)
    {
        $rows = $this->listAction->execute($request->validated());
        return ApiResponse::paginated(
            $rows,
            GoodsReceiptResource::collection($rows),
            "Goods receipts fetched successfully."
        );
    }
    public function show(string $uuid)
    {
        return ApiResponse::success(
            new GoodsReceiptResource($this->showAction->execute($uuid)),
            "Goods receipt fetched successfully."
        );
    }
    public function store(CreateGoodsReceiptRequest $request)
    {
        $userId = $request->user()?->id ? $request->user()?->id : Auth::id();
        return ApiResponse::success(
            new GoodsReceiptResource(
                $this->createAction->execute(
                    $request->validated(),
                    $userId
                )
            ),
            "Goods receipt created successfully."
        );
    }
    public function post(string $uuid)
    {
        $userId = request()->user()?->id ? request()->user()?->id : Auth::id();
        return ApiResponse::success(
            new GoodsReceiptResource(
                $this->postAction->execute($uuid, $userId)
            ),
            "Goods receipt posted and stock updated successfully."
        );
    }
    public function void(ReceiptVoidRequest $request, string $uuid)
    {
        $userId = $request->user()?->id ? $request->user()?->id : Auth::id();
        return ApiResponse::success(
            new GoodsReceiptResource(
                $this->voidAction->execute(
                    $uuid,
                    $userId,
                    $request->validated()["reason"]
                )
            ),
            "Goods receipt voided successfully."
        );
    }
}
