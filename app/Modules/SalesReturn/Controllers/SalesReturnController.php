<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\SalesReturn\Actions\CreateSalesReturnAction;
use App\Modules\SalesReturn\Actions\ListCustomerReturnsAction;
use App\Modules\SalesReturn\Actions\ShowCustomerReturnAction;
use App\Modules\SalesReturn\Requests\CreateSalesReturnRequest;
use App\Modules\SalesReturn\Resources\SalesReturnResource;
use Illuminate\Http\Request;

/** Customer sales-return endpoints. */
final class SalesReturnController extends Controller
{
    public function __construct(
        private readonly ListCustomerReturnsAction $listAction,
        private readonly CreateSalesReturnAction $createAction,
        private readonly ShowCustomerReturnAction $showAction,
    ) {}

    public function index(Request $request)
    {
        $returns = $this->listAction->execute($request->user()->id);

        return ApiResponse::success(
            SalesReturnResource::collection($returns),
            'Returns fetched successfully.'
        );
    }

    public function store(CreateSalesReturnRequest $request)
    {
        return ApiResponse::success(
            new SalesReturnResource($this->createAction->execute($request->validated(), $request->user()->id)),
            'Return request created successfully.'
        );
    }

    public function show(Request $request, string $uuid)
    {
        return ApiResponse::success(
            new SalesReturnResource($this->showAction->execute($request->user()->id, $uuid)),
            'Return fetched successfully.'
        );
    }
}
