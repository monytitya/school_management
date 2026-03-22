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

    // GET /api/students
    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $filters = [
            'class_id' => $_GET['class_id'] ?? null,
            'search'   => $_GET['search']   ?? null,
        ];

        $students = $this->studentModel->getAll($filters);
        Response::success($students, 'Students retrieved.');
    }

    // GET /api/students/:id
    public function show(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher', 'parent']);

        $student = $this->studentModel->findById((int)$id);
        if (!$student) Response::notFound('Student not found.');
        Response::success($student);
    }

    // POST /api/students
    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $body = $this->getBody();

        // Validate
        $errors = [];
        if (empty($body['name']))        $errors['name']        = 'Name is required.';
        if (empty($body['email']))       $errors['email']       = 'Email is required.';
        if (empty($body['password']))    $errors['password']    = 'Password is required.';
        if (empty($body['student_id']))  $errors['student_id']  = 'Student ID is required.';
        if (!empty($errors)) Response::error('Validation failed.', 422, $errors);

        if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL))
            Response::error('Invalid email format.', 422);

        if ($this->userModel->emailExists($body['email']))
            Response::error('Email already registered.', 409);

        if ($this->studentModel->studentIdExists($body['student_id']))
            Response::error('Student ID already exists.', 409);

        // Create user account
        $userId = $this->userModel->create(
            trim($body['name']),
            strtolower(trim($body['email'])),
            $body['password'],
            'student'
        );

        // Create student profile
        $studentId = $this->studentModel->create([
            'user_id'       => $userId,
            'student_id'    => $body['student_id'],
            'class_id'      => $body['class_id']      ?? null,
            'parent_id'     => $body['parent_id']      ?? null,
            'date_of_birth' => $body['date_of_birth']  ?? null,
            'gender'        => $body['gender']          ?? null,
            'phone'         => $body['phone']           ?? null,
            'address'       => $body['address']         ?? null,
            'enrolled_date' => $body['enrolled_date']   ?? date('Y-m-d'),
        ]);

        $student = $this->studentModel->findById($studentId);
        Response::success($student, 'Student created successfully.', 201);
    }

    // PUT /api/students/:id
    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $student = $this->studentModel->findById((int)$id);
        if (!$student) Response::notFound('Student not found.');

        $body = $this->getBody();
        $this->studentModel->update((int)$id, $body);

        // Update name/email on users table if provided
        if (!empty($body['name']) || !empty($body['email'])) {
            $db = Database::connect();
            if (!empty($body['name']) && !empty($body['email'])) {
                $db->prepare("UPDATE users SET name=?, email=? WHERE id=?")
                   ->execute([$body['name'], $body['email'], $student['user_id']]);
            } elseif (!empty($body['name'])) {
                $db->prepare("UPDATE users SET name=? WHERE id=?")
                   ->execute([$body['name'], $student['user_id']]);
            }
        }

        $updated = $this->studentModel->findById((int)$id);
        Response::success($updated, 'Student updated successfully.');
    }

    // DELETE /api/students/:id
    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $student = $this->studentModel->findById((int)$id);
        if (!$student) Response::notFound('Student not found.');

        $this->studentModel->delete((int)$id);
        Response::success(null, 'Student deleted successfully.');
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
