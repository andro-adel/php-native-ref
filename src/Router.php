<?php

namespace App;

use App\Http\Request;
use App\Http\Response;

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void    { $this->addRoute('GET', $path, $handler); }
    public function post(string $path, $handler): void   { $this->addRoute('POST', $path, $handler); }
    public function put(string $path, $handler): void    { $this->addRoute('PUT', $path, $handler); }
    public function patch(string $path, $handler): void  { $this->addRoute('PATCH', $path, $handler); }
    public function delete(string $path, $handler): void { $this->addRoute('DELETE', $path, $handler); }
    public function options(string $path, $handler): void{ $this->addRoute('OPTIONS', $path, $handler); }
    public function head(string $path, $handler): void   { $this->addRoute('HEAD', $path, $handler); }

    private function addRoute(string $method, string $path, $handler): void
    {
        $params = [];
        $regex = preg_replace_callback('/\{([^}]+)\}/', function ($m) use (&$params) {
            $params[] = $m[1];
            return '([^/]+)';
        }, $path);
        $pattern = "@^{$regex}$@";

        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
            'pattern' => $pattern,
            'params'  => $params,
        ];
    }

    public function dispatch(Request $request): void
    {
        $path   = $request->path;
        $method = $request->method;
        $allowed = [];

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            if ($route['method'] !== $method) {
                $allowed[] = $route['method'];
                continue;
            }

            array_shift($matches);
            $handler = $route['handler'];
            $args = array_merge([$request], $matches);

            try {
                if (is_callable($handler)) {
                    call_user_func_array($handler, $args);
                    return;
                }
                if (is_array($handler)) {
                    [$class, $m] = $handler;
                    $instance = new $class;
                    call_user_func_array([$instance, $m], $args);
                    return;
                }
            } catch (\Throwable $e) {
                // خطأ غير متوقع
                Response::json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
                return;
            }
        }

        if (!empty($allowed)) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            Response::json(['error' => 'Method Not Allowed'], 405);
            return;
        }

        Response::json(['error' => 'Not Found'], 404);
    }
}
