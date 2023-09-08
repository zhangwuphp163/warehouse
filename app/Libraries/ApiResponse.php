<?php

namespace App\Libraries;

class ApiResponse
{
    public static function success($data = [], $message = "Success", $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => 200,
            'msg' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error($message = "", $statusCode = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => $statusCode,
            'msg' => $message,
        ], 200);
    }
}
