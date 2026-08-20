<?php

declare(strict_types=1);
namespace App\Modules\Purchase\Controllers;
use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use App\Modules\Purchase\Requests\PurchaseInvoiceListRequest;
use App\Modules\Purchase\Requests\CreatePurchaseInvoiceRequest;
use App\Modules\Purchase\Services\PurchaseInvoiceService;
use App\Modules\Purchase\Resources\PurchaseInvoiceResource;
class PurchaseInvoiceController extends Controller
{
    public function __construct(
        private readonly PurchaseInvoiceService $service
    ) {
    }
    public function index(PurchaseInvoiceListRequest $request)
    {
        $p = $this->service->list($request->validated());
        return ApiResponse::paginated(
            $p,
            PurchaseInvoiceResource::collection($p),
            "Purchase invoices fetched successfully."
        );
    }
    public function show(string $uuid)
    {
        return ApiResponse::success(
            new PurchaseInvoiceResource($this->service->find($uuid)),
            "Purchase invoice fetched successfully."
        );
    }
    public function store(CreatePurchaseInvoiceRequest $request)
    {
        return ApiResponse::success(
            new PurchaseInvoiceResource(
                $this->service->create($request->validated())
            ),
            "Purchase invoice created successfully."
        );
    }
    public function post(string $uuid)
    {
        return ApiResponse::success(
            new PurchaseInvoiceResource($this->service->post($uuid)),
            "Purchase invoice posted successfully."
        );
    }
}
