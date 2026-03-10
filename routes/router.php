<?php

require_once __DIR__ . '/../controllers/AuthController.php';

class Router {

    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void {
        // Strip query string
        $path = parse_url($uri, PHP_URL_PATH);
        // Remove base prefix /api
        $path = preg_replace('#^/api#', '', $path);

        foreach ($this->routes as $route) {
            $pattern = $this->toRegex($route['path']);
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                // Extract named params
                array_shift($matches);
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Route $method $path not found."]);
    }

    // Convert /users/:id  →  regex
    private function toRegex(string $path): string {
        $pattern = preg_replace('#:([a-zA-Z_]+)#', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
