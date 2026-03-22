<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class AuthController
{

    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function register(): void
    {
        $body = $this->getBody();

        $errors = [];
        if (empty($body['name']))     $errors['name']     = 'Name is required.';
        if (empty($body['email']))    $errors['email']    = 'Email is required.';
        if (empty($body['password'])) $errors['password'] = 'Password is required.';
        if (empty($body['role']))     $errors['role']     = 'Role is required.';

        if (!empty($errors)) Response::error('Validation failed.', 422, $errors);

        // Validate email format
        if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email format.', 422);
        }

        // Validate password length
        if (strlen($body['password']) < 6) {
            Response::error('Password must be at least 6 characters.', 422);
        }

        // Validate role
        $allowedRoles = ['admin', 'teacher', 'student', 'parent'];
        if (!in_array($body['role'], $allowedRoles)) {
            Response::error('Invalid role. Allowed: admin, teacher, student, parent.', 422);
        }

        // Check email already exists
        if ($this->userModel->emailExists($body['email'])) {
            Response::error('Email already registered.', 409);
        }

        // Create user
        try {
            $id = $this->userModel->create(
                trim($body['name']),
                strtolower(trim($body['email'])),
                $body['password'],
                $body['role']
            );

            $user = $this->userModel->findById($id);
            $token = JWT::generate(['user_id' => $user['id'], 'role' => $user['role'], 'email' => $user['email']]);

            Response::success([
                'user'  => $user,
                'token' => $token,
            ], 'Registration successful.', 201);
        } catch (PDOException $e) {
            Response::error('Database Error: ' . $e->getMessage(), 500);
        }
    }

    // POST /api/auth/login
    public function login(): void
    {
        $body = $this->getBody();

        if (empty($body['email']) || empty($body['password'])) {
            Response::error('Email and password are required.', 422);
        }

        $user = $this->userModel->findByEmail(strtolower(trim($body['email'])));

        if (!$user || !password_verify($body['password'], $user['password'])) {
            Response::error('Invalid email or password.', 401);
        }

        $token = JWT::generate([
            'user_id' => $user['id'],
            'role'    => $user['role'],
            'email'   => $user['email'],
        ]);

        // Remove password from response
        unset($user['password']);

        Response::success([
            'user'       => $user,
            'token'      => $token,
            'expires_in' => JWT_EXPIRY,
        ], 'Login successful.');
    }

    // GET /api/auth/me  (protected)
    public function me(): void
    {
        $payload = AuthMiddleware::authenticate();
        $user = $this->userModel->findById($payload['user_id']);
        if (!$user) Response::notFound('User not found.');
        Response::success($user, 'Authenticated user.');
    }

    // POST /api/auth/change-password  (protected)
    public function changePassword(): void
    {
        $payload = AuthMiddleware::authenticate();
        $body = $this->getBody();

        if (empty($body['current_password']) || empty($body['new_password'])) {
            Response::error('current_password and new_password are required.', 422);
        }

        if (strlen($body['new_password']) < 6) {
            Response::error('New password must be at least 6 characters.', 422);
        }

        $user = $this->userModel->findByEmail($payload['email']);
        if (!password_verify($body['current_password'], $user['password'])) {
            Response::error('Current password is incorrect.', 401);
        }

        $this->userModel->updatePassword($payload['user_id'], $body['new_password']);
        Response::success(null, 'Password changed successfully.');
    }

    private function getBody(): array
    {
        if (!empty($_POST)) return $_POST;
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) return $json;
        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }
}
