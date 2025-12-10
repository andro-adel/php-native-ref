<?php

namespace App;

class Router
{
    private $routes = [];

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }
    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute($method, $path, $handler)
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch($uri, $method)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            $pattern = "@^" . preg_replace('/\{[^\}]+\}/', '([^/]+)', $route['path']) . "$@";
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    return call_user_func_array($handler, $matches);
                } elseif (is_array($handler)) {
                    [$class, $method] = $handler;
                    $instance = new $class;
                    return call_user_func_array([$instance, $method], $matches);
                }
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
