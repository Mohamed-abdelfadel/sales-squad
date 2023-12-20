<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HttpsResponses
{
    protected function success($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'successful',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error($message , $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

}
