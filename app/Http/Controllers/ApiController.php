<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{

    /**
     * Return generic json response with the given data
     *
     * @param $data
     * @param int $statusCode
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    protected function respond($data, $statusCode = 200, $message = '', $headers = [])
    {
        $newData = [];

        if (!isset($data['data'])) {
            if (is_array($data) && empty($data)) {
                $newData['data'] = (object)[];
            } else {
                $newData['data'] = $data;
            }
        } else {
            $newData = $data;
        }

        $newData['success'] = [
            'message' => $message
        ];

        return response()->json($newData, $statusCode, $headers);
    }

    /**
     * Respond with success
     *
     * @param $data
     * @param string $message
     * @return JsonResponse
     */
    protected function respondSuccess($data, $message = '')
    {
        return $this->respond($data, 200, $message);
    }

    /**
     * Respond with error
     *
     * @param $message
     * @param $statusCode
     * @return JsonResponse
     */
    protected function respondError($message, $statusCode)
    {
        return response()->json(['error' => $message], $statusCode);
    }

    /**
     * Respond with unauthorized
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respond with forbidden
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondForbidden($message = 'You do not have access to this resource')
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with Not found
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function respondNotFound($message = 'Not Found')
    {
        return $this->respondError($message, 404);
    }
}