<?php

ob_start();



set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    ob_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "PHP Error [$errno]: $errstr in $errfile:$errline"]);
    exit;
});

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/jwt.php';
require_once __DIR__ . '/helpers/response.php';
require_once __DIR__ . '/middleware/auth.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/routes/router.php';
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/api.php';

$router = new Router();

registerAuthRoutes($router);
registerStudentRoutes($router);
registerTeacherRoutes($router);
registerClassRoutes($router);
registerSubjectRoutes($router);
registerAttendanceRoutes($router);
registerGradeRoutes($router);
registerStudentRegistryRoutes($router);
registerStatsRoutes($router);
registerSchoolRoutes($router);
registerClassroomRoutes($router);
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$scriptName = $_SERVER['SCRIPT_NAME'];
$baseDir = dirname($scriptName);

if ($baseDir !== '/' && $baseDir !== '\\' && strpos($uri, $baseDir) === 0) {
    $uri = substr($uri, strlen($baseDir));
}

if (strpos($uri, '/index.php') === 0) {
    $uri = substr($uri, strlen('/index.php'));
}

$uri = $uri ?: '/';

$router->dispatch($method, $uri);
