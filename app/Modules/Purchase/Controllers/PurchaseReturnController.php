<?php
namespace App\Modules\Purchase\Controllers;
use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use App\Modules\Purchase\Requests\PurchaseReturnListRequest;
use App\Modules\Purchase\Requests\CreatePurchaseReturnRequest;
use App\Modules\Purchase\Services\PurchaseReturnService;
use App\Modules\Purchase\Resources\PurchaseReturnResource;
class PurchaseReturnController extends Controller
{
    public function __construct(private readonly PurchaseReturnService $service)
    {
    }
    public function index(PurchaseReturnListRequest $request)
    {
        $p = $this->service->list($request->validated());
        return ApiResponse::paginated(
            $p,
            PurchaseReturnResource::collection($p),
            "Purchase returns fetched successfully."
        );
    }
    public function show(string $uuid)
    {
        return ApiResponse::success(
            new PurchaseReturnResource($this->service->find($uuid)),
            "Purchase return fetched successfully."
        );
    }
    public function store(CreatePurchaseReturnRequest $request)
    {
        return ApiResponse::success(
            new PurchaseReturnResource(
                $this->service->create($request->validated())
            ),
            "Purchase return created successfully."
        );
    }
    public function post(string $uuid)
    {
        return ApiResponse::success(
            new PurchaseReturnResource($this->service->post($uuid)),
            "Purchase return posted successfully."
        );
    }
}
