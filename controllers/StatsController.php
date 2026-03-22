<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/StudentRegistryModel.php';
require_once __DIR__ . '/../models/TeacherModel.php';
require_once __DIR__ . '/../models/ClassModel.php';
require_once __DIR__ . '/../models/SubjectModel.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middleware/auth.php';

class StatsController
{
    public function dashboard(): void
    {
        $user = AuthMiddleware::authenticate();
        AuthMiddleware::authorize($user, ['admin', 'teacher']);

        $db = Database::connect();
        $registry = new StudentRegistryModel();
        $teachers = new TeacherModel();

        $classCount = (int) $db->query('SELECT COUNT(*) FROM classes')->fetchColumn();
        $subjectCount = (int) $db->query('SELECT COUNT(*) FROM subjects')->fetchColumn();

        Response::success([
            'student_registry_count' => $registry->count(),
            'teachers_count'         => $teachers->count(),
            'classes_count'          => $classCount,
            'subjects_count'         => $subjectCount,
        ], 'Dashboard stats.');
    }
}
