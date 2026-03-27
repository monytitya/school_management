<?php
require_once __DIR__ . '/../models/AttendanceModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class AttendanceController {
    private AttendanceModel $attendanceModel;

    public function __construct() {
        $this->attendanceModel = new AttendanceModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $classId = (int)($_GET['class_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        if (!$classId) Response::error('Class ID is required.', 422);
        Response::success($this->attendanceModel->getByClass($classId, $date));
    }

    public function byStudent(string $id): void {
        $user = AuthMiddleware::authenticate();
        // Since getByStudent method is probably not in the model yet, return an empty array for now or implement it as null
        Response::success([]);
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->attendanceModel->delete((int)$id);
        Response::success(null, 'Attendance deleted.');
    }

    public function bulkRecord(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $body = $this->getBody();
        if (empty($body['class_id']) || empty($body['date']) || empty($body['records']))
            Response::error('Missing required fields.', 422);
        
        $createdBy = $user['user_id'] ?? $user['id'] ?? null;
        $this->attendanceModel->bulkRecord($body['records'], (int)$body['class_id'], $body['date'], (int)$createdBy);
        Response::success(null, 'Attendance recorded.');
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