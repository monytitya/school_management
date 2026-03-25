<?php
require_once __DIR__ . '/../models/GradeModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class GradeController {
    private GradeModel $gradeModel;

    public function __construct() {
        $this->gradeModel = new GradeModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $subId = (int)($_GET['subject_id'] ?? 0);
        $term = $_GET['term'] ?? '';
        if (!$subId) Response::error('Subject ID required.', 422);
        Response::success($this->gradeModel->getBySubject($subId, $term));
    }

    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $body = $this->getBody();
        if (empty($body['student_id']) || empty($body['subject_id']) || !isset($body['score']) || empty($body['term']))
            Response::error('All fields required.', 422);
        
        $body['created_by'] = $user['id'];
        $id = $this->gradeModel->create($body);
        Response::success($this->gradeModel->findById($id), 'Grade recorded.', 201);
    }

    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $body = $this->getBody();
        $this->gradeModel->update((int)$id, $body);
        Response::success($this->gradeModel->findById((int)$id), 'Updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $this->gradeModel->delete((int)$id);
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
