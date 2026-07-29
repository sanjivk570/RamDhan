<?php

namespace App\Core\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generate standardized API JSON responses.
 *
 * This utility provides a consistent response structure for
 * successful operations, errors, validation failures,
 * authorization failures, and paginated resources.
 *
 * @package App\Core\Responses
 * @author Sanjiv Kumar Kushwaha
 */
final class ApiResponse
{
    /**
     * Build a standardized JSON response.
     *
     * @param bool $success Indicates whether the request was successful.
     * @param string $message The response message.
     * @param mixed $data The response payload.
     * @param mixed $errors Validation or application errors.
     * @param mixed $meta Additional response metadata.
     * @param int $status The HTTP status code.
     * @return JsonResponse
     */
    private static function make(bool $success, string $message, mixed $data = null, mixed $errors = null, mixed $meta = null, int $status = Response::HTTP_OK): JsonResponse {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
            'errors'  => $errors,
            'meta'    => $meta,
        ], $status);
    }

    /**
     * Return a successful response.
     *
     * @param mixed $data The response payload.
     * @param string $message The success message.
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $message = 'Success'): JsonResponse 
    {
        return self::make(
            success: true,
            message: $message,
            data: $data
        );
    }

    /**
     * Return a resource created response.
     *
     * @param mixed $data The created resource.
     * @param string $message The success message.
     * @return JsonResponse
     */
    public static function created(mixed $data = null, string $message = 'Created successfully.'): JsonResponse 
    {
        return self::make(
            success: true,
            message: $message,
            data: $data,
            status: Response::HTTP_CREATED
        );
    }

    /**
     * Return a resource updated response.
     *
     * @param mixed $data The updated resource.
     * @param string $message The success message.
     * @return JsonResponse
     */
    public static function updated(mixed $data = null, string $message = 'Updated successfully.'): JsonResponse 
    {
        return self::make(
            success: true,
            message: $message,
            data: $data
        );
    }

    /**
     * Return a resource deleted response.
     *
     * @param string $message The success message.
     * @return JsonResponse
     */
    public static function deleted(string $message = 'Deleted successfully.'): JsonResponse 
    {
        return self::make(
            success: true,
            message: $message
        );
    }

    /**
     * Return a validation error response.
     *
     * @param mixed $errors The validation errors.
     * @param string $message The error message.
     * @return JsonResponse
     */
    public static function validationError(mixed $errors, string $message = 'Validation failed.'): JsonResponse 
    {
        return self::make(
            success: false,
            message: $message,
            errors: $errors,
            status: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Return an unauthorized response.
     *
     * @param string $message The error message.
     * @return JsonResponse
     */
    public static function unauthorized(string $message = 'Unauthenticated.'): JsonResponse 
    {
        return self::make(
            success: false,
            message: $message,
            status: Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Return a forbidden response.
     *
     * @param string $message The error message.
     * @return JsonResponse
     */
    public static function forbidden(string $message = 'Forbidden.'): JsonResponse 
    {
        return self::make(
            success: false,
            message: $message,
            status: Response::HTTP_FORBIDDEN
        );
    }

    /**
     * Return a resource not found response.
     *
     * @param string $message The error message.
     * @return JsonResponse
     */
    public static function notFound(string $message = 'Resource not found.'): JsonResponse 
    {
        return self::make(
            success: false,
            message: $message,
            status: Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Return an error response.
     *
     * @param string $message The error message.
     * @param mixed $errors Additional error details.
     * @param int $status The HTTP status code.
     * @return JsonResponse
     */
    public static function error(string $message = 'Something went wrong.', mixed $errors = null, int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse 
    {
        return self::make(
            success: false,
            message: $message,
            errors: $errors,
            status: $status
        );
    }

    /**
     * Return a paginated response.
     *
     * @param LengthAwarePaginator $paginator The paginator instance.
     * @param ResourceCollection|JsonResource|array $data The paginated data.
     * @param string $message The success message.
     * @return JsonResponse
     */
    public static function paginated(LengthAwarePaginator $paginator, ResourceCollection|JsonResource|array $data, string $message = 'Success'): JsonResponse 
    {
        return self::make(
            success: true,
            message: $message,
            data: $data,
            meta: [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ]
        );
    }
}