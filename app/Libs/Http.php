<?php

namespace App\Libs;


class Http
{
    const ERROR_CODE_BAD_REQUEST = 400;
    const ERROR_CODE_UN_AUTHORIZED = 401;
    const ERROR_CODE_FORBIDDEN = 403;
    const ERROR_CODE_NOT_FOUND = 404;
    const ERROR_CODE_SESSION_TIMEOUT = 100;

    public static function responseMessage($message = '')
    {
        return response()->json([
            'message' => $message,
        ], 200, [], JSON_PRETTY_PRINT);
    }


    public static function responseError($message = '', $headerCode = 404)
    {
        return response()->json([
            'error' => ['code' => $headerCode, 'message' => $message],
        ], 200, [], JSON_PRETTY_PRINT);
    }

    public static function responseData($data = [])
    {
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
}
