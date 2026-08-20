<?php

declare(strict_types=1);

namespace App\Modules\Customer\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;
use App\Modules\Customer\Actions\ListCustomerAddressAction;
use App\Modules\Customer\Actions\ShowCustomerAddressAction;
use App\Modules\Customer\Actions\CreateCustomerAddressAction;
use App\Modules\Customer\Actions\UpdateCustomerAddressAction;
use App\Modules\Customer\Actions\DeleteCustomerAddressAction;
use App\Modules\Customer\Actions\SetDefaultCustomerAddressAction;
use App\Modules\Customer\Requests\CustomerAddressListRequest;
use App\Modules\Customer\Requests\CreateCustomerAddressRequest;
use App\Modules\Customer\Requests\UpdateCustomerAddressRequest;
use App\Modules\Customer\Resources\CustomerAddressResource;

class CustomerAddressController extends Controller
{
    public function __construct(
        private readonly ListCustomerAddressAction $listAction,
        private readonly ShowCustomerAddressAction $showAction,
        private readonly CreateCustomerAddressAction $createAction,
        private readonly UpdateCustomerAddressAction $updateAction,
        private readonly DeleteCustomerAddressAction $deleteAction,
        private readonly SetDefaultCustomerAddressAction $setDefaultAction
    ) {
    }

    /**
     * GET /customers/addresses
     */
    public function index(CustomerAddressListRequest $request)
    {
        $customer = $request->user();

        $addresses = $this->listAction->execute(
            $customer->id,
            $request->validated()
        );

        return ApiResponse::paginated(
            $addresses,
            CustomerAddressResource::collection($addresses),
            "Customer addresses fetched successfully."
        );
    }

    /**
     * GET /customers/addresses/{uuid}
     */
    public function show(string $uuid, CustomerAddressListRequest $request)
    {
        $customer = $request->user();

        $address = $this->showAction->execute($customer->id, $uuid);

        return ApiResponse::success(
            new CustomerAddressResource($address),
            "Customer address fetched successfully."
        );
    }

    /**
     * POST /customers/addresses
     */
    public function store(CreateCustomerAddressRequest $request)
    {
        $customer = $request->user();

        $address = $this->createAction->execute(
            $customer->id,
            $request->validated()
        );

        return ApiResponse::success(
            new CustomerAddressResource($address),
            "Customer address created successfully."
        );
    }

    /**
     * PUT /customers/addresses/{uuid}
     */
    public function update(UpdateCustomerAddressRequest $request, string $uuid)
    {
        $customer = $request->user();

        $address = $this->updateAction->execute(
            $customer->id,
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new CustomerAddressResource($address),
            "Customer address updated successfully."
        );
    }

    /**
     * DELETE /customers/addresses/{uuid}
     */
    public function destroy(string $uuid, CustomerAddressListRequest $request)
    {
        $customer = $request->user();

        $this->deleteAction->execute($customer->id, $uuid);

        return ApiResponse::success(
            [],
            "Customer address deleted successfully."
        );
    }

    /**
     * PATCH /customers/addresses/{uuid}/default
     */
    public function setDefault(
        string $uuid,
        CustomerAddressListRequest $request
    ) {
        $customer = $request->user();

        $address = $this->setDefaultAction->execute($customer->id, $uuid);

        return ApiResponse::success(
            new CustomerAddressResource($address),
            "Customer default address updated successfully."
        );
    }
}
