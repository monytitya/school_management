<?php
require_once __DIR__ . '/../models/ClassroomModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class ClassroomController {
    private ClassroomModel $classroomModel;

    public function __construct() {
        $this->classroomModel = new ClassroomModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        Response::success($this->classroomModel->getAll(), 'Classrooms retrieved.');
    }

    public function show(string $id): void {
        $user = AuthMiddleware::authenticate();
        $room = $this->classroomModel->findById((int)$id);
        if (!$room) Response::notFound('Classroom not found.');
        Response::success($room);
    }

    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $body = $this->getBody();
        if (empty($body['name'])) Response::error('Room Name is required.', 422);

        $id = $this->classroomModel->create([
            'name' => $body['name'],
            'capacity' => $body['capacity'] ?? 30
        ]);
        Response::success($this->classroomModel->findById($id), 'Classroom created.', 201);
    }

    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $id = (int)$id;
        $room = $this->classroomModel->findById($id);
        if (!$room) Response::notFound();

        $body = $this->getBody();
        $this->classroomModel->update($id, [
            'name' => $body['name'] ?? $room['name'],
            'capacity' => $body['capacity'] ?? $room['capacity']
        ]);
        Response::success($this->classroomModel->findById($id), 'Classroom updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->classroomModel->delete((int)$id);
        Response::success(null, 'Classroom deleted.');
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
