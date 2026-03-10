<?php

require_once __DIR__ . '/../models/TeacherModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class TeacherController
{

    private TeacherModel $teacherModel;
    private UserModel    $userModel;

    public function __construct()
    {
        $this->teacherModel = new TeacherModel();
        $this->userModel    = new UserModel();
    }


    public function index(): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $filters = ['search' => $_GET['search'] ?? null];
        $teachers = $this->teacherModel->getAll($filters);
        Response::success($teachers, 'Teachers retrieved.');
    }

    public function show(string $id): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $teacher = $this->teacherModel->findById((int)$id);
        if (!$teacher) Response::notFound('Teacher not found.');
        Response::success($teacher);
    }


    public function store(): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $body = $this->getBody();

        $errors = [];
        if (empty($body['name']))        $errors['name']        = 'Name is required.';
        if (empty($body['email']))       $errors['email']       = 'Email is required.';
        if (empty($body['password']))    $errors['password']    = 'Password is required.';
        if (empty($body['employee_id'])) $errors['employee_id'] = 'Employee ID is required.';
        if (!empty($errors)) Response::error('Validation failed.', 422, $errors);

        if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL))
            Response::error('Invalid email format.', 422);

        if ($this->userModel->emailExists($body['email']))
            Response::error('Email already registered.', 409);

        if ($this->teacherModel->employeeIdExists($body['employee_id']))
            Response::error('Employee ID already exists.', 409);

        $userId = $this->userModel->create(
            trim($body['name']),
            strtolower(trim($body['email'])),
            $body['password'],
            'teacher'
        );

        $teacherId = $this->teacherModel->create([
            'user_id'     => $userId,
            'employee_id' => $body['employee_id'],
            'phone'       => $body['phone']       ?? null,
            'address'     => $body['address']     ?? null,
            'joined_date' => $body['joined_date'] ?? date('Y-m-d'),
        ]);

        $teacher = $this->teacherModel->findById($teacherId);
        Response::success($teacher, 'Teacher created successfully.', 201);
    }


    public function update(string $id): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $teacher = $this->teacherModel->findById((int)$id);
        if (!$teacher) Response::notFound('Teacher not found.');

        $body = $this->getBody();
        $this->teacherModel->update((int)$id, $body);

        if (!empty($body['name'])) {
            Database::connect()
                ->prepare("UPDATE users SET name=? WHERE id=?")
                ->execute([$body['name'], $teacher['user_id']]);
        }

        Response::success($this->teacherModel->findById((int)$id), 'Teacher updated.');
    }

    public function destroy(string $id): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin']);

        $teacher = $this->teacherModel->findById((int)$id);
        if (!$teacher) Response::notFound('Teacher not found.');

        $this->teacherModel->delete((int)$id);
        Response::success(null, 'Teacher deleted successfully.');
    }

    private function getBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}
