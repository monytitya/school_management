<?php
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class ClassController {
    private ClassModel $classModel;

    public function __construct() {
        $this->classModel = new ClassModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        Response::success($this->classModel->getAll(), 'Classes retrieved.');
    }

    public function show(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $class = $this->classModel->findById((int)$id);
        if (!$class) Response::notFound('Class not found.');
        Response::success($class);
    }

    public function students(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $students = $this->classModel->getStudents((int)$id);
        Response::success($students, 'Class students retrieved.');
    }

    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $body = $this->getBody();
        $name = $body['class_name'] ?? $body['name'] ?? '';
        $grade = $body['grade_level'] ?? '';
        if (empty($name) || empty($grade)) Response::error('Name and Grade Level required.', 422);

        $id = $this->classModel->create([
            'class_name' => $name, 'grade_level' => $grade,
            'teacher_id' => $body['teacher_id'] ?? null,
            'academic_year' => $body['academic_year'] ?? date('Y') . '-' . (date('Y') + 1)
        ]);
        Response::success($this->classModel->findById($id), 'Class created.', 201);
    }

    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $id = (int)$id;
        $c = $this->classModel->findById($id);
        if (!$c) Response::notFound();
        $body = $this->getBody();
        $this->classModel->update($id, [
            'class_name' => $body['class_name'] ?? $body['name'] ?? $c['class_name'],
            'grade_level' => $body['grade_level'] ?? $c['grade_level'],
            'teacher_id' => $body['teacher_id'] ?? $c['teacher_id'],
            'academic_year' => $body['academic_year'] ?? $c['academic_year']
        ]);
        Response::success($this->classModel->findById($id), 'Updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->classModel->delete((int)$id);
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
