<?php

declare(strict_types=1);

namespace App\Modules\SalesInvoice\Controllers;

use App\Core\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\SalesInvoice\Actions\ListCustomerInvoicesAction;
use App\Modules\SalesInvoice\Actions\ShowCustomerInvoiceAction;
use App\Modules\SalesInvoice\Resources\SalesInvoiceResource;
use Illuminate\Http\Request;

/** Customer invoice endpoints. */
final class SalesInvoiceController extends Controller
{
    public function __construct(
        private readonly ListCustomerInvoicesAction $listAction,
        private readonly ShowCustomerInvoiceAction $showAction,
    ) {}

    public function index(Request $request)
    {
        $invoices = $this->listAction->execute($request->user()->id);

        return ApiResponse::success(
            SalesInvoiceResource::collection($invoices),
            'Invoices fetched successfully.'
        );
    }

    public function show(Request $request, string $uuid)
    {
        return ApiResponse::success(
            new SalesInvoiceResource($this->showAction->execute($request->user()->id, $uuid)),
            'Invoice fetched successfully.'
        );
    }
}
