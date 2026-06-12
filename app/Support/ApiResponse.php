<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\MessageBag;

class ApiResponse
{
    /**
     * Return a successful JSON response.
     */
    public static function success(
        mixed $data = null,
        string $message = 'OK',
        int $status = JsonResponse::HTTP_OK,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a JSON response from an API resource or resource collection.
     */
    public static function resource(
        JsonResource $resource,
        string $message = 'OK',
        int $status = JsonResponse::HTTP_OK,
    ): JsonResponse {
        $payload = $resource->response()->getData(true);

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $payload['data'] ?? $payload,
        ];

        if (isset($payload['links'])) {
            $response['links'] = $payload['links'];
        }

        if (isset($payload['meta'])) {
            $response['meta'] = $payload['meta'];
        }

        return response()->json($response, $status);
    }

    /**
     * Return a validation error JSON response.
     */
    public static function validationError(
        MessageBag $errors,
        string $message = 'Validation errors.',
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors->toArray(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
