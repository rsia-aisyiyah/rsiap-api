<?php

namespace App;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Success response
     * 
     * @param mixed $data
     * @param string $message
     * @param int $code
     * 
     * @return \Illuminate\Http\JsonResponse
     * */
    public static function success($data, $message = null, $code = 200)
    {
        return self::responseTemplate(true, $message, $data, $code);
    }

    /**
     * Fail response
     * 
     * @param string $message
     * @param int $code
     * 
     * @return \Illuminate\Http\JsonResponse
     * */
    public static function fail($message = null, $code = 400)
    {
        return self::responseTemplate(false, $message, null, $code);
    }

    /**
     * Not found response
     * 
     * @param string $message
     * 
     * @return \Illuminate\Http\JsonResponse
     * */
    public static function notFound($message)
    {
        return self::responseTemplate(false, $message, null, 404);
    }

    /**
     * Ok response
     * 
     * @param string $message
     * 
     * @return \Illuminate\Http\JsonResponse
     * */
    public static function ok($message)
    {
        return self::responseTemplate(true, $message, null, 200);
    }

    /**
     * Created response
     * 
     * @param mixed $data
     * @param string $message
     * 
     * @return \Illuminate\Http\JsonResponse
     * */
    public static function unauthenticated($message = 'Unauthenticated')
    {
        return self::responseTemplate(false, $message, null, 401);
    }

    /**
     * Forbidden response
     * 
     * @param string $message
     * 
     * @return \Illuminate\Http\JsonResponse
     * */
    public static function forbidden($message = 'Forbidden')
    {
        return self::responseTemplate(false, $message, null, 403);
    }


    /**
     * Response template
     * 
     * @param bool $success
     * @param string $message
     * @param mixed $data
     * @param int $code
     * 
     * @return \Illuminate\Http\JsonResponse
     * */
    private static function responseTemplate($success, $message, $data, $code)
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
