<?php

class Router
{
    private array $routes = [];


    public function get(string $path, $handler): void
    {
        $this->add('GET', $path, $handler);
    }
    public function post(string $path, $handler): void
    {
        $this->add('POST', $path, $handler);
    }
    public function put(string $path, $handler): void
    {
        $this->add('PUT', $path, $handler);
    }
    public function delete(string $path, $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function add(string $method, string $path, $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        $path = parse_url($uri, PHP_URL_PATH);
        $path = preg_replace('#^/api#', '', $path);
        $path = $path === '' ? '/' : rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            $pattern = $this->toRegex($route['path']);

            if ($route['method'] === strtoupper($method) && preg_match($pattern, $path, $matches)) {

                array_shift($matches);

                if (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $matches);
                }
                return;
            }
        }
        $this->sendNotFound($method, $path);
    }

    private function toRegex(string $path): string
    {
        $pattern = preg_replace('#:([a-zA-Z_]+)#', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function sendNotFound($method, $path): void
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => "Route $method $path not found."
        ]);
    }
}