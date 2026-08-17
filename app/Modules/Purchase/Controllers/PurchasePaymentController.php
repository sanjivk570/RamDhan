<?php
namespace App\Modules\Purchase\Controllers;
use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use App\Modules\Purchase\Requests\PurchasePaymentListRequest;
use App\Modules\Purchase\Requests\CreatePurchasePaymentRequest;
use App\Modules\Purchase\Services\PurchasePaymentService;
class PurchasePaymentController extends Controller
{
    public function __construct(
        private readonly PurchasePaymentService $service
    ) {
    }
    public function index(PurchasePaymentListRequest $request)
    {
        $p = $this->service->list($request->validated());
        return ApiResponse::paginated(
            $p,
            $p->items(),
            "Purchase payments fetched successfully."
        );
    }
    public function show(string $uuid)
    {
        return ApiResponse::success(
            $this->service->find($uuid),
            "Purchase payment fetched successfully."
        );
    }
    public function store(CreatePurchasePaymentRequest $request)
    {
        return ApiResponse::success(
            $this->service->create($request->validated()),
            "Purchase payment posted successfully."
        );
    }
}
