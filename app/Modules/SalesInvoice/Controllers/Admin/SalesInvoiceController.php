<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Controllers\Admin;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
use App\Modules\SalesInvoice\Actions\AdminListInvoicesAction;
use App\Modules\SalesInvoice\Actions\AdminShowInvoiceAction;
use App\Modules\SalesInvoice\Actions\GenerateInvoiceAction;
use App\Modules\SalesInvoice\Resources\SalesInvoiceResource;
use Illuminate\Http\Request;

/** Administrative invoice endpoints. */
final class SalesInvoiceController extends Controller
{
    public function __construct(
        private readonly AdminListInvoicesAction $listAction,
        private readonly AdminShowInvoiceAction $showAction,
        private readonly GenerateInvoiceAction $generateAction,
    ) {}

    public function index(Request $request)
    {
        $invoices = $this->listAction->execute($request->all());

        return ApiResponse::paginated(
            $invoices,
            SalesInvoiceResource::collection($invoices),
            'Invoices fetched successfully.'
        );
    }

    public function show(string $uuid)
    {
        return ApiResponse::success(
            new SalesInvoiceResource($this->showAction->execute($uuid)),
            'Invoice fetched successfully.'
        );
    }

    public function generate(string $orderUuid)
    {
        $order = Order::where('uuid', $orderUuid)->with('items')->firstOrFail();

        return ApiResponse::success(
            new SalesInvoiceResource($this->generateAction->execute($order)),
            'Invoice generated successfully.'
        );
    }
}
