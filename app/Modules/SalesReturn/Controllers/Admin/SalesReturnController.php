<?php

declare(strict_types=1);

namespace App\Modules\SalesReturn\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\SalesReturn\Actions\AdminListReturnsAction;
use App\Modules\SalesReturn\Actions\ProcessSalesReturnAction;
use App\Modules\SalesReturn\Models\SalesReturn;
use App\Modules\SalesReturn\Requests\ProcessSalesReturnRequest;
use App\Modules\SalesReturn\Requests\SalesReturnListRequest;
use App\Modules\SalesReturn\Resources\SalesReturnResource;
use Illuminate\Http\Request;

/** Administrative return and refund workflow endpoints. */
final class SalesReturnController extends Controller
{
    public function __construct(
        private readonly AdminListReturnsAction $listAction,
        private readonly ProcessSalesReturnAction $processAction,
    ) {}

    public function index(SalesReturnListRequest $request)
    {
        $returns = $this->listAction->execute($request->validated());

        return ApiResponse::paginated(
            $returns,
            SalesReturnResource::collection($returns),
            'Returns fetched successfully.'
        );
    }

    public function process(ProcessSalesReturnRequest $request, string $uuid)
    {
        $return = SalesReturn::where('uuid', $uuid)->firstOrFail();

        return ApiResponse::success(
            new SalesReturnResource($this->processAction->execute(
                $return,
                $request->string('action')->toString(),
                $request->input('admin_note'),
                $request->user()->id,
            )),
            'Return processed successfully.'
        );
    }
}
