<?php
class HttpStatus
{
    const OK = 200;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INTERNAL_SERVER_ERROR = 500;

    const Conflict = 409;

    const  Created = 201;

    private static $messages = [
        200 => 'OK',
        201 => 'Created',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        409 => 'Conflict',
        500 => 'Internal Server Error',
    ];


    public static function setStatus(int $code)
    {
        http_response_code($code);
    }

    public static function getMessage(int $code): string
    {
        return self::$messages[$code] ?? 'Unknown Status';
    }
}
