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
        $stageId = $body['stage_id'] ?? null;
        $sectionId = $body['section_id'] ?? null;
        
        if (empty($name)) Response::error('Class Name is required.', 422);
        if (empty($stageId)) Response::error('Academic Stage (Grade) is required.', 422);
        if (empty($sectionId)) Response::error('Section is required.', 422);
        
        $stageId = (int)$stageId;
        $sectionId = (int)$sectionId;

        $id = $this->classModel->create([
            'class_name' => $name, 
            'stage_id' => $stageId,
            'section_id' => $sectionId,
            'teacher_id' => $body['teacher_id'] ?? null,
            'subject_id' => $body['subject_id'] ?? null,
            'classroom_id' => $body['classroom_id'] ?? null
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
        $name = $body['class_name'] ?? $body['name'] ?? $c['class_name'];
        $stageId = !empty($body['stage_id']) ? (int)$body['stage_id'] : $c['stage_id'];
        $sectionId = !empty($body['section_id']) ? (int)$body['section_id'] : $c['section_id'];

        if (empty($name)) Response::error('Class Name cannot be empty.', 422);
        if (empty($stageId)) Response::error('Academic Stage is required.', 422);
        if (empty($sectionId)) Response::error('Section is required.', 422);

        $this->classModel->update($id, [
            'class_name' => $name,
            'stage_id' => $stageId,
            'section_id' => $sectionId,
            'teacher_id' => $body['teacher_id'] ?? $c['teacher_id'],
            'subject_id' => array_key_exists('subject_id', $body) ? $body['subject_id'] : $c['subject_id'],
            'classroom_id' => array_key_exists('classroom_id', $body) ? $body['classroom_id'] : $c['classroom_id']
        ]);
        Response::success($this->classModel->findById($id), 'Updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->classModel->delete((int)$id);
        Response::success(null, 'Deleted.');
    }

    public function metadata(): void {
        $user = AuthMiddleware::authenticate();
        $db = Database::connect();
        $stages = $db->query("SELECT stage_id as id, stage_name as name FROM stages")->fetchAll();
        $sections = $db->query("SELECT section_id as id, section_name as name FROM sections")->fetchAll();
        $classrooms = $db->query("SELECT classroom_id as id, room_name as name FROM classrooms")->fetchAll();
        $subjects = $db->query("SELECT subject_id as id, title as name FROM subjects")->fetchAll();
        
        Response::success([
            'stages' => $stages,
            'sections' => $sections,
            'classrooms' => $classrooms,
            'subjects' => $subjects
        ]);
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
