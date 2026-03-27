<?php
require_once __DIR__ . '/../models/SchoolModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class SchoolController {
    private SchoolModel $schoolModel;

    public function __construct() {
        $this->schoolModel = new SchoolModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        Response::success($this->schoolModel->getAll(), 'Schools retrieved.');
    }

    public function show(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $school = $this->schoolModel->findById((int)$id);
        if (!$school) Response::notFound('School not found.');
        Response::success($school);
    }

    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $body = $this->getBody();
        if (empty($body['school_title'])) Response::error('School Title is required.', 422);

        $id = $this->schoolModel->create([
            'school_title' => $body['school_title'],
            'level_count' => $body['level_count'] ?? 0,
            'is_active' => $body['is_active'] ?? 1
        ]);
        Response::success($this->schoolModel->findById($id), 'School created.', 201);
    }

    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $id = (int)$id;
        $school = $this->schoolModel->findById($id);
        if (!$school) Response::notFound();

        $body = $this->getBody();
        $this->schoolModel->update($id, [
            'school_title' => $body['school_title'] ?? $school['school_title'],
            'level_count' => $body['level_count'] ?? $school['level_count'],
            'is_active' => $body['is_active'] ?? $school['is_active']
        ]);
        Response::success($this->schoolModel->findById($id), 'School updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->schoolModel->delete((int)$id);
        Response::success(null, 'School deleted.');
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
