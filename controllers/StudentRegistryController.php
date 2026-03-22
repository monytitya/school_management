<?php

require_once __DIR__ . '/../models/StudentRegistryModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class StudentRegistryController
{
    private StudentRegistryModel $model;

    public function __construct()
    {
        $this->model = new StudentRegistryModel();
    }

    public function index(): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher', 'student', 'parent']);

        $filters = [
            'search'   => $_GET['search'] ?? null,
            'school_id'=> $_GET['school_id'] ?? null,
        ];
        $rows = $this->model->getAll($filters);
        Response::success($rows, 'Student registry retrieved.');
    }

    public function show(string $id): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher', 'student', 'parent']);

        $row = $this->model->findById((int) $id);
        if (!$row) {
            Response::notFound('Record not found.');
        }
        Response::success($row);
    }

    public function store(): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $body = $this->getBody();
        $errors = [];
        if (empty($body['student_code'])) {
            $errors['student_code'] = 'Student code is required.';
        }
        if (empty($body['student_full_name'])) {
            $errors['student_full_name'] = 'Full name is required.';
        }
        if (!empty($errors)) {
            Response::error('Validation failed.', 422, $errors);
        }

        if (!empty($body['email']) && !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email format.', 422);
        }

        if ($this->model->codeExists(trim($body['student_code']))) {
            Response::error('Student code already exists.', 409);
        }

        $data = $this->normalizeBody($body);
        $id = $this->model->create($data);
        Response::success($this->model->findById($id), 'Student added to registry.', 201);
    }

    public function update(string $id): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $existing = $this->model->findById((int) $id);
        if (!$existing) {
            Response::notFound('Record not found.');
        }

        $body = $this->getBody();
        $errors = [];
        if (empty($body['student_code'])) {
            $errors['student_code'] = 'Student code is required.';
        }
        if (empty($body['student_full_name'])) {
            $errors['student_full_name'] = 'Full name is required.';
        }
        if (!empty($errors)) {
            Response::error('Validation failed.', 422, $errors);
        }

        if (!empty($body['email']) && !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email format.', 422);
        }

        if ($this->model->codeExists(trim($body['student_code']), (int) $id)) {
            Response::error('Student code already exists.', 409);
        }

        $this->model->update((int) $id, $this->normalizeBody($body));
        Response::success($this->model->findById((int) $id), 'Record updated.');
    }

    public function destroy(string $id): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        if (!$this->model->findById((int) $id)) {
            Response::notFound('Record not found.');
        }
        $this->model->delete((int) $id);
        Response::success(null, 'Record deleted.');
    }

    private function normalizeBody(array $body): array
    {
        $g = $body['gender'] ?? null;
        if ($g === '') {
            $g = null;
        }
        if ($g !== null && !in_array($g, ['male', 'female', 'other'], true)) {
            Response::error('Invalid gender.', 422);
        }

        return [
            'student_code'      => trim($body['student_code']),
            'student_full_name' => trim($body['student_full_name']),
            'gender'            => $g,
            'dob'               => !empty($body['dob']) ? $body['dob'] : null,
            'email'             => !empty($body['email']) ? trim($body['email']) : null,
            'phone'             => !empty($body['phone']) ? trim($body['phone']) : null,
            'school_id'         => $this->optionalInt($body['school_id'] ?? null),
            'stage_id'          => $this->optionalInt($body['stage_id'] ?? null),
            'section_id'        => $this->optionalInt($body['section_id'] ?? null),
        ];
    }

    private function optionalInt($v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        return (int) $v;
    }

    private function getBody(): array
    {
        if (!empty($_POST)) {
            return $_POST;
        }
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return $json;
        }
        parse_str($raw, $parsed);
        return is_array($parsed) ? $parsed : [];
    }
}
