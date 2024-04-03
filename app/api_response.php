<?php
// success response
if (!function_exists('isSuccess')) {
    function isSuccess($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}

// fail response
if (!function_exists('isFail')) {
    function isFail($message = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}

// not found response
if (!function_exists('isNotFound')) {
    function isNotFound(string $message)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 404);
    }
}

// ok response
if (!function_exists('isOk')) {
    function isOk(string $message)
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }
}

// // unauthenticated response
if (!function_exists('isUnauthenticated')) {
    function isUnauthenticated(string $message = 'Unauthenticated')
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 401);
    }
}

// forbidden response
if (!function_exists('isForbidden')) {
    function isForbidden(string $message = 'Forbidden')
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 403);
    }
}

// error server response
if (!function_exists('isError')) {
    function isError(string $message = 'Internal Server Error')
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 500);
    }
}

//debug return 
if (!function_exists('debugReturn')) {
    function debugReturn($data, $message = "Debug", $code = 200)
    {
        return response()->json([
            'debug'     => true,
            'date'      => date('Y-m-d H:i:s'),
            'message'   => $message,
            'data'      => $data
        ], $code);
    }
}
