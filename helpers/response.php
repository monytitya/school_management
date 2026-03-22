<?php

class Response
{
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    public static function success($data = null, string $message = 'Success', int $code = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
    public static function error(string $message = 'Error', int $code = 400, $errors = null): void
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];
        if ($errors !== null) $body['errors'] = $errors;
        self::json($body, $code);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error($message, 403);
    }

    public static function notFound(string $message = 'Not found'): void
    {
        self::error($message, 404);
    }
}
