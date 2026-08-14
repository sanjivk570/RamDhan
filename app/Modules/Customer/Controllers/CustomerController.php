<?php

declare(strict_types=1);

namespace App\Modules\Customer\Controllers;

use App\Http\Controllers\Controller;
use App\Core\Responses\ApiResponse;

use App\Modules\Customer\Actions\ListCustomerAction;
use App\Modules\Customer\Actions\ShowCustomerAction;
use App\Modules\Customer\Actions\CreateCustomerAction;
use App\Modules\Customer\Actions\UpdateCustomerAction;
use App\Modules\Customer\Actions\DeleteCustomerAction;
use App\Modules\Customer\Actions\RestoreCustomerAction;
use App\Modules\Customer\Actions\ChangeStatusAction;

use App\Modules\Customer\Requests\CustomerListRequest;
use App\Modules\Customer\Requests\CreateCustomerRequest;
use App\Modules\Customer\Requests\UpdateCustomerRequest;
use App\Modules\Customer\Requests\ChangeStatusRequest;

use App\Modules\Customer\Resources\CustomerResource;

use App\Modules\Customer\Actions\UpdateCustomerProfileAction;
use App\Modules\Customer\Requests\UpdateCustomerProfileRequest;

class CustomerController extends Controller
{
    public function __construct(
        private readonly ListCustomerAction $listCustomerAction,
        private readonly ShowCustomerAction $showCustomerAction,
        private readonly CreateCustomerAction $createCustomerAction,
        private readonly UpdateCustomerAction $updateCustomerAction,
        private readonly DeleteCustomerAction $deleteCustomerAction,
        private readonly RestoreCustomerAction $restoreCustomerAction,
        private readonly ChangeStatusAction $changeStatusAction,
        private readonly UpdateCustomerProfileAction $updateCustomerProfileAction
    ) {
    }

    public function index(CustomerListRequest $request)
    {
        $customers = $this->listCustomerAction->execute($request->validated());

        return ApiResponse::paginated(
            $customers,
            CustomerResource::collection($customers),
            "Customers fetched successfully."
        );
    }

    public function show(string $uuid)
    {
        $customer = $this->showCustomerAction->execute($uuid);

        return ApiResponse::success(
            new CustomerResource($customer),
            "Customer fetched successfully."
        );
    }

    public function store(CreateCustomerRequest $request)
    {
        $customer = $this->createCustomerAction->execute($request->validated());

        return ApiResponse::success(
            new CustomerResource($customer),
            "Customer created successfully."
        );
    }

    public function update(UpdateCustomerRequest $request, string $uuid)
    {
        $customer = $this->updateCustomerAction->execute(
            $uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new CustomerResource($customer),
            "Customer updated successfully."
        );
    }

    // public function changeStatus(string $uuid, bool $status)
    // {
    //     $customer = $this->changeStatusAction->execute($uuid, $status);

    //     return ApiResponse::success(
    //         new CustomerResource($customer),
    //         "Customer status updated successfully."
    //     );
    // }

    public function changeStatus(ChangeStatusRequest $request,string $uuid) {
        $customer = $this->changeStatusAction
                ->execute(
                    $uuid,
                    $request->boolean('status')
                );

        return ApiResponse::success(
            new CustomerResource($customer),
            'Customer status updated successfully.'
        );
    }

    public function destroy(string $uuid)
    {
        $this->deleteCustomerAction->execute($uuid);

        return ApiResponse::success([], "Customer deleted successfully.");
    }

    public function restore(string $uuid)
    {
        $customer = $this->restoreCustomerAction->execute($uuid);

        return ApiResponse::success(
            new CustomerResource($customer),
            "Customer restored successfully."
        );
    }

    /**
     * Update authenticated customer profile.
     *
     * PUT /api/v1/customer/profile
     */
    public function updateProfile(
        UpdateCustomerProfileRequest $request
    ) {

        $customer = $request->user();

        $updatedCustomer = $this->updateCustomerProfileAction->execute(
            $customer->uuid,
            $request->validated()
        );

        return ApiResponse::success(
            new CustomerResource($updatedCustomer),
            'Customer profile updated successfully.'
        );
    }
}
