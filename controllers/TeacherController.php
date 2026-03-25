<?php
require_once __DIR__ . '/../models/TeacherModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class TeacherController {
    private TeacherModel $teacherModel;

    public function __construct() {
        $this->teacherModel = new TeacherModel();
    }

    public function index(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $filters = ['search' => $_GET['search'] ?? null];
        Response::success($this->teacherModel->getAll($filters), 'Teachers retrieved.');
    }

    public function show(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);
        $teacher = $this->teacherModel->findById((int)$id);
        if (!$teacher) Response::notFound('Teacher not found.');
        Response::success($teacher);
    }

    public function store(): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $body = $this->getBody();
        $name = $body['teacher_full_name'] ?? $body['name'] ?? '';
        $code = $body['teacher_code'] ?? $body['employee_id'] ?? '';
        
        if (empty($name) || empty($code)) {
            $missing = [];
            if (empty($name)) $missing[] = 'Name';
            if (empty($code)) $missing[] = 'Code/Employee ID';
            $debug = "POST: " . print_r($_POST, true) . ", FILES: " . print_r($_FILES, true);
            Response::error(implode(' and ', $missing) . ' are required. (' . $debug . ')', 422);
        }

        if ($this->teacherModel->codeExists($code)) Response::error('Code already exists.', 409);

        require_once __DIR__ . '/../helpers/upload.php';
        $image = null;
        if (!empty($_FILES['profile_image'])) {
            $image = UploadHelper::uploadImage($_FILES['profile_image']);
        }

        $id = $this->teacherModel->create([
            'teacher_code' => $code, 'teacher_full_name' => $name,
            'gender' => $body['gender'] ?? null, 'dob' => $body['dob'] ?? null,
            'email' => $body['email'] ?? null, 'phone' => $body['phone'] ?? null,
            'profile_image' => $image,
            'joined_date' => $body['joined_date'] ?? null, 'address' => $body['address'] ?? null
        ]);
        Response::success($this->teacherModel->findById($id), 'Teacher created.', 201);
    }

    public function update(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $body = $this->getBody();
        $id = (int)$id;
        $t = $this->teacherModel->findById($id);
        if (!$t) Response::notFound();

        require_once __DIR__ . '/../helpers/upload.php';
        $image = null;
        if (!empty($_FILES['profile_image']['name'])) {
            $image = UploadHelper::uploadImage($_FILES['profile_image']);
        }
        
        $this->teacherModel->update($id, [
            'teacher_full_name' => $body['teacher_full_name'] ?? $body['name'] ?? $t['teacher_full_name'],
            'teacher_code' => $body['teacher_code'] ?? $body['employee_id'] ?? $t['teacher_code'],
            'gender' => $body['gender'] ?? $t['gender'],
            'dob' => $body['dob'] ?? $t['dob'],
            'email' => $body['email'] ?? $t['email'],
            'phone' => $body['phone'] ?? $t['phone'],
            'profile_image' => $image,
            'old_profile_image' => $t['profile_image'] ?? null,
            'joined_date' => $body['joined_date'] ?? $t['joined_date'],
            'address' => $body['address'] ?? $t['address']
        ]);
        Response::success($this->teacherModel->findById($id), 'Updated.');
    }

    public function destroy(string $id): void {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);
        $this->teacherModel->delete((int)$id);
        Response::success(null, 'Deleted.');
    }

    private function getBody(): array {
        // Start with $_POST and $_FILES (populated by PHP for multipart and urlencoded)
        $body = array_merge($_POST, $_FILES);
        
        // Try to get data from input stream (for JSON or raw data)
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $json = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                $body = array_merge($body, $json);
            } else {
                parse_str($raw, $parsed);
                if (is_array($parsed)) {
                    $body = array_merge($body, $parsed);
                }
            }
        }
        
        // Remove the internal _method if present in the data body
        if (isset($body['_method'])) unset($body['_method']);
        
        return $body;
    }
}
