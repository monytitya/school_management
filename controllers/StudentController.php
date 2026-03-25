<?php
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class StudentController {
    private StudentModel $studentModel;
    private UserModel    $userModel;

    public function __construct() {
        $this->studentModel = new StudentModel();
        $this->userModel    = new UserModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $filters = ['class_id' => $_GET['class_id'] ?? null, 'search' => $_GET['search'] ?? null];
        Response::success($this->studentModel->getAll($filters), 'Students retrieved.');
    }

    public function show(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher', 'parent']);
        $student = $this->studentModel->findById((int)$id);
        if (!$student) Response::notFound('Student not found.');
        Response::success($student);
    }

    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $body = $this->getBody();
        $name = $body['student_full_name'] ?? $body['name'] ?? '';
        $email = $body['email'] ?? '';
        $pwd = $body['password'] ?? '';
        $code = $body['student_code'] ?? $body['student_id'] ?? '';
        if (empty($name) || empty($email) || empty($pwd) || empty($code)) Response::error('All fields required.', 422);
        if ($this->userModel->emailExists($email)) Response::error('Email registered.', 409);
        if ($this->studentModel->codeExists($code)) Response::error('Code already exists.', 409);
        $userId = $this->userModel->create($name, $email, $pwd, 'student');
        $id = $this->studentModel->create([
            'user_id' => $userId, 'student_code' => $code, 'student_full_name' => $name,
            'gender' => $body['gender'] ?? null, 'dob' => $body['dob'] ?? null,
            'email' => $email, 'phone' => $body['phone'] ?? null, 'address' => $body['address'] ?? null,
            'enrolled_date' => $body['enrolled_date'] ?? null, 'class_id' => $body['class_id'] ?? null
        ]);
        Response::success($this->studentModel->findById($id), 'Student created.', 201);
    }

    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $id = (int)$id; $s = $this->studentModel->findById($id);
        if (!$s) Response::notFound(); $body = $this->getBody();
        $this->studentModel->update($id, [
            'student_code' => $body['student_code'] ?? $body['student_id'] ?? $s['student_code'],
            'student_full_name' => $body['student_full_name'] ?? $body['name'] ?? $s['student_full_name'],
            'gender' => $body['gender'] ?? $s['gender'], 'dob' => $body['dob'] ?? $s['dob'],
            'phone' => $body['phone'] ?? $s['phone'], 'address' => $body['address'] ?? $s['address'],
            'class_id' => $body['class_id'] ?? $s['class_id']
        ]);
        Response::success($this->studentModel->findById($id), 'Updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->studentModel->delete((int)$id);
        Response::success(null, 'Deleted.');
    }

    private function getBody(): array {
        if (!empty($_POST)) return $_POST;
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) return $json;
        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }
}
