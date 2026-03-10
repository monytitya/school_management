<?php

require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class ClassController {

    private ClassModel $classModel;

    public function __construct() {
        $this->classModel = new ClassModel();
    }

    // GET /api/classes
    public function index(): void {
        AuthMiddleware::authenticate();
        Response::success($this->classModel->getAll(), 'Classes retrieved.');
    }

    // GET /api/classes/:id
    public function show(string $id): void {
        AuthMiddleware::authenticate();
        $class = $this->classModel->findById((int)$id);
        if (!$class) Response::notFound('Class not found.');
        Response::success($class);
    }

    // GET /api/classes/:id/students
    public function students(string $id): void {
        AuthMiddleware::authenticate();
        $class = $this->classModel->findById((int)$id);
        if (!$class) Response::notFound('Class not found.');
        $students = $this->classModel->getStudents((int)$id);
        Response::success($students, 'Class students retrieved.');
    }

    // POST /api/classes
    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $body = $this->getBody();
        if (empty($body['name']) || empty($body['grade_level']))
            Response::error('name and grade_level are required.', 422);

        $id    = $this->classModel->create($body);
        $class = $this->classModel->findById($id);
        Response::success($class, 'Class created.', 201);
    }

    // PUT /api/classes/:id
    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $class = $this->classModel->findById((int)$id);
        if (!$class) Response::notFound('Class not found.');

        $body = $this->getBody();
        if (empty($body['name']) || empty($body['grade_level']))
            Response::error('name and grade_level are required.', 422);

        $this->classModel->update((int)$id, $body);
        Response::success($this->classModel->findById((int)$id), 'Class updated.');
    }

    // DELETE /api/classes/:id
    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        if (!$this->classModel->findById((int)$id)) Response::notFound('Class not found.');
        $this->classModel->delete((int)$id);
        Response::success(null, 'Class deleted.');
    }

    private function getBody(): array {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}
