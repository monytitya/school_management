<?php

require_once __DIR__ . '/../models/StudentRegistryModel.php';
require_once __DIR__ . '/../config/database.php';
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
            'school_id' => $_GET['school_id'] ?? null,
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

    //Store and Update
    // public function store(): void
    // {
    //     $user = AuthMiddleware::authenticate();
    //     AuthMiddleware::authorize($user, ['admin', 'teacher']);

    //     $body = $this->getBody();
    //     $errors = [];
    //     if (empty($body['student_code'])) {
    //         $errors['student_code'] = 'Student code is required.';
    //     }
    //     if (empty($body['student_full_name'])) {
    //         $errors['student_full_name'] = 'Full name is required.';
    //     }
    //     if (!empty($errors)) {
    //         Response::error('Validation failed.', 422, $errors);
    //     }

    //     if (!empty($body['email']) && !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
    //         Response::error('Invalid email format.', 422);
    //     }

    //     if ($this->model->codeExists(trim($body['student_code']))) {
    //         Response::error('Student code already exists.', 409);
    //     }

    //     $data = $this->normalizeBody($body);
    //     $fkErrors = $this->validateForeignKeys($data);
    //     if (!empty($fkErrors)) {
    //         Response::error('Validation failed.', 422, $fkErrors);
    //     }

    //     try {
    //         $id = $this->model->create($data);
    //         Response::success($this->model->findById($id), 'Student added to registry.', 201);
    //     } catch (Throwable $e) {
    //         Response::error('Could not save student. Please verify school/stage/section values.', 422);
    //     }
    // }

    // public function update(string $id): void
    // {
    //     $user = AuthMiddleware::authenticate();
    //     AuthMiddleware::authorize($user, ['admin', 'teacher']);

    //     $existing = $this->model->findById((int) $id);
    //     if (!$existing) {
    //         Response::notFound('Record not found.');
    //     }

    //     $body = $this->getBody();
    //     $errors = [];
    //     if (empty($body['student_code'])) {
    //         $errors['student_code'] = 'Student code is required.';
    //     }
    //     if (empty($body['student_full_name'])) {
    //         $errors['student_full_name'] = 'Full name is required.';
    //     }
    //     if (!empty($errors)) {
    //         Response::error('Validation failed.', 422, $errors);
    //     }

    //     if (!empty($body['email']) && !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
    //         Response::error('Invalid email format.', 422);
    //     }

    //     if ($this->model->codeExists(trim($body['student_code']), (int) $id)) {
    //         Response::error('Student code already exists.', 409);
    //     }

    //     $data = $this->normalizeBody($body);
    //     $fkErrors = $this->validateForeignKeys($data);
    //     if (!empty($fkErrors)) {
    //         Response::error('Validation failed.', 422, $fkErrors);
    //     }

    //     try {
    //         $this->model->update((int) $id, $data);
    //         Response::success($this->model->findById((int) $id), 'Record updated.');
    //     } catch (Throwable $e) {
    //         Response::error('Could not update student. Please verify school/stage/section values.', 422);
    //     }
    // }

    public function store(): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $body = $this->getBody();

        // 1. Validation មូលដ្ឋាន
        $errors = [];
        if (empty($body['student_code'])) $errors['student_code'] = 'Student code is required.';
        if (empty($body['student_full_name'])) $errors['student_full_name'] = 'Full name is required.';

        if (!empty($errors)) {
            Response::error('Validation failed.', 422, $errors);
        }

        if (!empty($body['email']) && !filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email format.', 422);
        }

        if ($this->model->codeExists(trim($body['student_code']))) {
            Response::error('Student code already exists.', 409);
        }

        // 2. រៀបចំទិន្នន័យ និង Check Foreign Keys
        $data = $this->normalizeBody($body);
        $fkErrors = $this->validateForeignKeys($data);

        if (!empty($fkErrors)) {
            Response::error('Foreign Key Validation failed.', 422, $fkErrors);
        }

        // 3. ព្យាយាម Save ចូល Database
        try {
            $id = $this->model->create($data);
            Response::success($this->model->findById($id), 'Student added successfully.', 201);
        } catch (Throwable $e) {
            // ប្តូរមកដាក់បែបនេះ ដើម្បីមើលកំហុសពិត (ដូចជា Unknown Column ជាដើម)
            Response::error("Database Error: " . $e->getMessage(), 422);
        }
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
        $data = $this->normalizeBody($body);

        // Check Foreign Keys មុននឹង Update
        $fkErrors = $this->validateForeignKeys($data);
        if (!empty($fkErrors)) {
            Response::error('Validation failed.', 422, $fkErrors);
        }

        try {
            $this->model->update((int) $id, $data);
            Response::success($this->model->findById((int) $id), 'Record updated successfully.');
        } catch (Throwable $e) {
            Response::error("Database Error: " . $e->getMessage(), 422);
        }
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
    private function validateForeignKeys(array $data): array
    {
        $errors = [];
        if (($data['school_id'] ?? null) !== null && !$this->existsById('schools', 'school_id', (int) $data['school_id'])) {
            $errors['school_id'] = 'School ID does not exist.';
        }
        if (($data['stage_id'] ?? null) !== null && !$this->existsById('stages', 'stage_id', (int) $data['stage_id'])) {
            $errors['stage_id'] = 'Stage ID does not exist.';
        }
        if (($data['section_id'] ?? null) !== null && !$this->existsById('sections', 'section_id', (int) $data['section_id'])) {
            $errors['section_id'] = 'Section ID does not exist.';
        }
        return $errors;
    }
    // private function validateForeignKeys(array $data): array
    // {
    //     $errors = [];
    //     if (($data['school_id'] ?? null) !== null && !$this->existsById('schools', 'id', (int) $data['school_id'])) {
    //         $errors['school_id'] = 'School ID does not exist.';
    //     }

    //     if (($data['stage_id'] ?? null) !== null && !$this->existsById('stages', 'id', (int) $data['stage_id'])) {
    //         $errors['stage_id'] = 'Stage ID does not exist.';
    //     }
    //     if (($data['section_id'] ?? null) !== null && !$this->existsById('sections', 'id', (int) $data['section_id'])) {
    //         $errors['section_id'] = 'Section ID does not exist.';
    //     }

    //     return $errors;
    // }
    private function existsById(string $table, string $idColumn, int $id): bool
    {
        $db = Database::connect();
        $tbl = str_replace('`', '', $table);
        $col = str_replace('`', '', $idColumn);
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM `{$tbl}` WHERE `{$col}` = ? LIMIT 1");
            $stmt->execute([$id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (Throwable $e) {
            return true;
        }
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
