<?php

require_once __DIR__ . '/../controllers/StudentController.php';
require_once __DIR__ . '/../controllers/TeacherController.php';
require_once __DIR__ . '/../controllers/ClassController.php';
require_once __DIR__ . '/../controllers/SubjectController.php';
require_once __DIR__ . '/../controllers/AttendanceController.php';
require_once __DIR__ . '/../controllers/GradeController.php';
require_once __DIR__ . '/../controllers/StudentRegistryController.php';
require_once __DIR__ . '/../controllers/StatsController.php';
require_once __DIR__ . '/../controllers/SchoolController.php';
require_once __DIR__ . '/../controllers/ClassroomController.php';


function registerStudentRoutes($router): void
{
    $router->get('/', function () {
        echo json_encode([
        'success' => true,
        'message' => 'School Management API is running!!'
        ]);
    });

    $ctrl = new StudentController();
    $router->add('GET', '/students', fn() => $ctrl->index());
    $router->add('GET', '/students/:id', fn($id) => $ctrl->show($id));
    $router->add('POST', '/students', fn() => $ctrl->store());
    $router->add('PUT', '/students/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/students/:id', fn($id) => $ctrl->destroy($id));
}

function registerTeacherRoutes(Router $router): void
{
    $ctrl = new TeacherController();
    $router->add('GET', '/teachers', fn() => $ctrl->index());
    $router->add('GET', '/teachers/:id', fn($id) => $ctrl->show($id));
    $router->add('POST', '/teachers', fn() => $ctrl->store());
    $router->add('PUT', '/teachers/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/teachers/:id', fn($id) => $ctrl->destroy($id));
}

function registerClassRoutes(Router $router): void
{
    $ctrl = new ClassController();
    $router->add('GET', '/classes', fn() => $ctrl->index());
    $router->add('GET', '/classes/:id', fn($id) => $ctrl->show($id));
    $router->add('GET', '/classes/:id/students', fn($id) => $ctrl->students($id));
    $router->add('GET', '/classes/metadata', fn() => $ctrl->metadata());
    $router->add('POST', '/classes', fn() => $ctrl->store());
    $router->add('PUT', '/classes/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/classes/:id', fn($id) => $ctrl->destroy($id));
}

function registerSubjectRoutes(Router $router): void
{
    $ctrl = new SubjectController();
    $router->add('GET', '/subjects', fn() => $ctrl->index());
    $router->add('GET', '/subjects/:id', fn($id) => $ctrl->show($id));
    $router->add('POST', '/subjects', fn() => $ctrl->store());
    $router->add('PUT', '/subjects/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/subjects/:id', fn($id) => $ctrl->destroy($id));
}
function registerAttendanceRoutes(Router $router): void
{
    $ctrl = new AttendanceController();
    $router->add('GET', '/attendance', fn() => $ctrl->index());
    $router->add('GET', '/attendance/student/:id', fn($id) => $ctrl->byStudent($id));
    $router->add('POST', '/attendance/bulk', fn() => $ctrl->bulkRecord());
    $router->add('DELETE', '/attendance/:id', fn($id) => $ctrl->destroy($id));
}

function registerGradeRoutes(Router $router): void
{
    $ctrl = new GradeController();
    $router->add('GET', '/grades/student/:id', fn($id) => $ctrl->byStudent($id));
    $router->add('GET', '/grades/subject/:id', fn($id) => $ctrl->bySubject($id));
    $router->add('GET', '/grades/report-card/:id', fn($id) => $ctrl->reportCard($id));
    $router->add('GET', '/grades/class-average/:subjectId', fn($id) => $ctrl->classAverage($id));
    $router->add('GET', '/grades/:id', fn($id) => $ctrl->show($id));
    $router->add('POST', '/grades', fn() => $ctrl->store());
    $router->add('PUT', '/grades/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/grades/:id', fn($id) => $ctrl->destroy($id));
}

function registerStudentRegistryRoutes(Router $router): void
{
    $ctrl = new StudentRegistryController();
    $router->add('GET', '/student-registry', fn() => $ctrl->index());
    $router->add('GET', '/student-registry/metadata', fn() => $ctrl->metadata());
    $router->add('GET', '/student-registry/:id', fn($id) => $ctrl->show($id));
    $router->add('POST', '/student-registry', fn() => $ctrl->store());
    $router->add('PUT', '/student-registry/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/student-registry/:id', fn($id) => $ctrl->destroy($id));
}

function registerStatsRoutes(Router $router): void
{
    $ctrl = new StatsController();
    $router->add('GET', '/stats/dashboard', fn() => $ctrl->dashboard());
}

function registerSchoolRoutes(Router $router): void
{
    $ctrl = new SchoolController();
    $router->add('GET', '/schools', fn() => $ctrl->index());
    $router->add('GET', '/schools/:id', fn($id) => $ctrl->show($id));
    $router->add('POST', '/schools', fn() => $ctrl->store());
    $router->add('PUT', '/schools/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/schools/:id', fn($id) => $ctrl->destroy($id));
}

function registerClassroomRoutes(Router $router): void
{
    $ctrl = new ClassroomController();
    $router->add('GET', '/classrooms', fn() => $ctrl->index());
    $router->add('GET', '/classrooms/:id', fn($id) => $ctrl->show($id));
    $router->add('POST', '/classrooms', fn() => $ctrl->store());
    $router->add('PUT', '/classrooms/:id', fn($id) => $ctrl->update($id));
    $router->add('DELETE', '/classrooms/:id', fn($id) => $ctrl->destroy($id));
}