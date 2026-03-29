<?php

define('DB_HOST', 'localhost');
define('DB_PORT', 3308);
define('DB_NAME', 'school_management');
define('DB_USER', 'root');
define('DB_PASS', 'mony2024**2000');

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                http_response_code(500);

                error_log($e->getMessage());

                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed'
                ]);
                exit;
            }
        }

        return self::$instance;
    }
}