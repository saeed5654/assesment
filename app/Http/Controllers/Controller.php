<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Defined constants for status codes
     */
    const SUCCESS = 200;
    const BAD_REQUEST = 400;
    const UN_AUTHORIZED = 401;
    const NOT_FOUND = 404;
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * Return json response with message only
     *
     * @param $status
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    public function withMessage($status, $message, $code)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $code);
    }

    /**
     * Return json response with message and data
     *
     * @param $status
     * @param $message
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    public function withMessageAndData($status, $message, $data, $code)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Return json response with data only
     *
     * @param $status
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    public function withData($status, $data, $code)
    {
        return response()->json([
            'status' => $status,
            'data' => $data
        ], $code);
    }

    /**
     * Return json response (user with token and data)
     *
     * @param $status
     * @param $message
     * @param $data
     * @param $token
     * @param $code
     * @return JsonResponse
     */
    public function withTokenAndData($status, $message, $data, $token, $code)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'token' => $token
        ], $code);
    }

    /**
     * Return json response with authentication token and message
     *
     * @param $status
     * @param $message
     * @param $token
     * @param $code
     * @return JsonResponse
     */
    public function withToken($status, $message, $token, $code)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'token' => $token
        ], $code);
    }
}
