<?php
require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../helpers/response.php';
class AuthMiddleware
{
    public static function authenticate(): array
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            Response::unauthorized('No token provided. Please login.');
        }
        $token = trim(substr($authHeader, 7));
        $payload = JWT::verify($token);

        if (!$payload) {
            Response::unauthorized('Invalid or expired token. Please login again.');
        }
        return $payload;
    }
    public static function authorize(array $user, array $allowedRoles): void
    {
        if (!in_array($user['role'], $allowedRoles)) {
            Response::forbidden('You do not have permission to access this resource.');
        }
    }
}
