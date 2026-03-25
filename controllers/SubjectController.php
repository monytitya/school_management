<?php
require_once __DIR__ . '/../models/SubjectModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class SubjectController {
    private SubjectModel $subjectModel;

    public function __construct() {
        $this->subjectModel = new SubjectModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $filters = ['class_id' => $_GET['class_id'] ?? null, 'teacher_id' => $_GET['teacher_id'] ?? null];
        Response::success($this->subjectModel->getAll($filters), 'Subjects retrieved.');
    }

    public function show(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $sub = $this->subjectModel->findById((int)$id);
        if (!$sub) Response::notFound('Subject not found.');
        Response::success($sub);
    }

    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $body = $this->getBody();
        $name = $body['subject_name'] ?? $body['name'] ?? '';
        if (empty($name)) Response::error('Subject Name is required.', 422);
        
        $id = $this->subjectModel->create([
            'name' => $name
        ]);
        Response::success($this->subjectModel->findById($id), 'Subject created.', 201);
    }

    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $id = (int)$id; $s = $this->subjectModel->findById($id);
        if (!$s) Response::notFound(); $body = $this->getBody();
        $this->subjectModel->update($id, [
            'name' => $body['subject_name'] ?? $body['name'] ?? $s['name']
        ]);
        Response::success($this->subjectModel->findById($id), 'Updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->subjectModel->delete((int)$id);
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
