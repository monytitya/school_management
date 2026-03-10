<?php

require_once __DIR__ . '/../models/GradeModel.php';
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class GradeController {

    private GradeModel   $gradeModel;
    private StudentModel $studentModel;

    public function __construct() {
        $this->gradeModel   = new GradeModel();
        $this->studentModel = new StudentModel();
    }

    // GET /api/grades/student/:id?term=Term+1
    public function byStudent(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher', 'parent', 'student']);

        $student = $this->studentModel->findById((int)$id);
        if (!$student) Response::notFound('Student not found.');

        $term   = $_GET['term']   ?? '';
        $grades = $this->gradeModel->getByStudent((int)$id, $term);

        Response::success($grades, 'Grades retrieved.');
    }

    // GET /api/grades/subject/:id?term=Term+1
    public function bySubject(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $term   = $_GET['term'] ?? '';
        $grades = $this->gradeModel->getBySubject((int)$id, $term);

        Response::success($grades, 'Subject grades retrieved.');
    }

    // GET /api/grades/report-card/:studentId?term=Term+1
    public function reportCard(string $studentId): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher', 'parent', 'student']);

        $student = $this->studentModel->findById((int)$studentId);
        if (!$student) Response::notFound('Student not found.');

        $term = $_GET['term'] ?? '';
        if (!$term) Response::error('term query parameter is required.', 422);

        $report = $this->gradeModel->getReportCard((int)$studentId, $term);

        // Calculate overall GPA
        $scores = array_column($report, 'average');
        $gpa    = count($scores) ? round(array_sum($scores) / count($scores), 2) : 0;

        Response::success([
            'student'  => $student,
            'term'     => $term,
            'subjects' => $report,
            'gpa'      => $gpa,
            'grade'    => $this->letterFromScore($gpa),
        ], 'Report card retrieved.');
    }

    // POST /api/grades
    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $body = $this->getBody();

        $errors = [];
        if (empty($body['student_id']))  $errors['student_id']  = 'student_id is required.';
        if (empty($body['subject_id']))  $errors['subject_id']  = 'subject_id is required.';
        if (!isset($body['score']))      $errors['score']       = 'score is required.';
        if (empty($body['term']))        $errors['term']        = 'term is required.';
        if (!empty($errors)) Response::error('Validation failed.', 422, $errors);

        if ($body['score'] < 0 || $body['score'] > 100)
            Response::error('Score must be between 0 and 100.', 422);

        $validTypes = ['quiz', 'midterm', 'final', 'assignment'];
        if (!empty($body['exam_type']) && !in_array($body['exam_type'], $validTypes))
            Response::error('Invalid exam_type. Use: quiz, midterm, final, assignment.', 422);

        $id    = $this->gradeModel->create([...$body, 'created_by' => $user['user_id']]);
        $grade = $this->gradeModel->findById($id);

        Response::success($grade, 'Grade recorded.', 201);
    }

    // PUT /api/grades/:id
    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $grade = $this->gradeModel->findById((int)$id);
        if (!$grade) Response::notFound('Grade not found.');

        $body = $this->getBody();

        if (isset($body['score']) && ($body['score'] < 0 || $body['score'] > 100))
            Response::error('Score must be between 0 and 100.', 422);

        $this->gradeModel->update((int)$id, array_merge($grade, $body));
        Response::success($this->gradeModel->findById((int)$id), 'Grade updated.');
    }

    // DELETE /api/grades/:id
    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $grade = $this->gradeModel->findById((int)$id);
        if (!$grade) Response::notFound('Grade not found.');

        $this->gradeModel->delete((int)$id);
        Response::success(null, 'Grade deleted.');
    }

    // GET /api/grades/class-average/:subjectId?term=Term+1
    public function classAverage(string $subjectId): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $term = $_GET['term'] ?? '';
        if (!$term) Response::error('term is required.', 422);

        $avg = $this->gradeModel->getClassAverage((int)$subjectId, $term);
        Response::success([
            'subject_id' => (int)$subjectId,
            'term'       => $term,
            'average'    => $avg,
            'grade'      => $avg !== null ? $this->letterFromScore($avg) : null,
        ]);
    }

    private function letterFromScore(float $score): string {
        return match(true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default      => 'F',
        };
    }

    private function getBody(): array {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}
