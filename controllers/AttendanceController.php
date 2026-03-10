<?php

require_once __DIR__ . '/../models/AttendanceModel.php';
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class AttendanceController {

    private AttendanceModel $attendanceModel;
    private StudentModel    $studentModel;

    public function __construct() {
        $this->attendanceModel = new AttendanceModel();
        $this->studentModel    = new StudentModel();
    }

    // GET /api/attendance?class_id=1&date=2024-09-01
    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $classId = $_GET['class_id'] ?? null;
        $date    = $_GET['date']     ?? date('Y-m-d');

        if (!$classId) Response::error('class_id is required.', 422);

        $records = $this->attendanceModel->getByClass((int)$classId, $date);
        Response::success($records, 'Attendance retrieved.');
    }

    // GET /api/attendance/student/:id
    public function byStudent(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher', 'parent', 'student']);

        $student = $this->studentModel->findById((int)$id);
        if (!$student) Response::notFound('Student not found.');

        $filters = [
            'month'  => $_GET['month']  ?? null,   // e.g. "2024-09"
            'status' => $_GET['status'] ?? null,
        ];

        $records = $this->attendanceModel->getByStudent((int)$id, $filters);
        $summary = $this->attendanceModel->getSummary((int)$id);

        Response::success([
            'records' => $records,
            'summary' => $summary,
        ], 'Student attendance retrieved.');
    }

    // POST /api/attendance  — record single
    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $body = $this->getBody();

        $errors = [];
        if (empty($body['student_id'])) $errors['student_id'] = 'student_id is required.';
        if (empty($body['class_id']))   $errors['class_id']   = 'class_id is required.';
        if (empty($body['date']))       $errors['date']       = 'date is required.';
        if (!empty($errors)) Response::error('Validation failed.', 422, $errors);

        $validStatuses = ['present', 'absent', 'late', 'excused'];
        if (!empty($body['status']) && !in_array($body['status'], $validStatuses))
            Response::error('Invalid status. Use: present, absent, late, excused.', 422);

        $this->attendanceModel->record([
            'student_id' => $body['student_id'],
            'class_id'   => $body['class_id'],
            'date'       => $body['date'],
            'status'     => $body['status']  ?? 'present',
            'note'       => $body['note']    ?? null,
            'created_by' => $user['user_id'],
        ]);

        Response::success(null, 'Attendance recorded.');
    }

    // POST /api/attendance/bulk  — record entire class at once
    public function bulk(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $body = $this->getBody();

        if (empty($body['class_id']))  Response::error('class_id is required.', 422);
        if (empty($body['date']))      Response::error('date is required.', 422);
        if (empty($body['records']) || !is_array($body['records']))
            Response::error('records array is required.', 422);

        $this->attendanceModel->bulkRecord(
            $body['records'],
            (int)$body['class_id'],
            $body['date'],
            $user['user_id']
        );

        Response::success(null, 'Bulk attendance recorded for ' . count($body['records']) . ' students.');
    }

    // DELETE /api/attendance/:id
    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $this->attendanceModel->delete((int)$id);
        Response::success(null, 'Attendance record deleted.');
    }

    private function getBody(): array {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}
