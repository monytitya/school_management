<?php

require_once __DIR__ . '/../models/SubjectModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class SubjectController {

    private SubjectModel $subjectModel;

    public function __construct() {
        $this->subjectModel = new SubjectModel();
    }

    // GET /api/subjects
    public function index(): void {
        AuthMiddleware::authenticate();
        $filters = [
            'class_id'   => $_GET['class_id']   ?? null,
            'teacher_id' => $_GET['teacher_id'] ?? null,
        ];
        Response::success($this->subjectModel->getAll($filters), 'Subjects retrieved.');
    }

    // GET /api/subjects/:id
    public function show(string $id): void {
        AuthMiddleware::authenticate();
        $subject = $this->subjectModel->findById((int)$id);
        if (!$subject) Response::notFound('Subject not found.');
        Response::success($subject);
    }

    // POST /api/subjects
    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $body = $this->getBody();
        if (empty($body['name']) || empty($body['code']))
            Response::error('name and code are required.', 422);

        if ($this->subjectModel->codeExists($body['code']))
            Response::error('Subject code already exists.', 409);

        $id      = $this->subjectModel->create($body);
        $subject = $this->subjectModel->findById($id);
        Response::success($subject, 'Subject created.', 201);
    }

    // PUT /api/subjects/:id
    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $subject = $this->subjectModel->findById((int)$id);
        if (!$subject) Response::notFound('Subject not found.');

        $body = $this->getBody();
        if (empty($body['name']) || empty($body['code']))
            Response::error('name and code are required.', 422);

        if ($this->subjectModel->codeExists($body['code'], (int)$id))
            Response::error('Subject code already exists.', 409);

        $this->subjectModel->update((int)$id, $body);
        Response::success($this->subjectModel->findById((int)$id), 'Subject updated.');
    }

    // DELETE /api/subjects/:id
    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        if (!$this->subjectModel->findById((int)$id)) Response::notFound('Subject not found.');
        $this->subjectModel->delete((int)$id);
        Response::success(null, 'Subject deleted.');
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
