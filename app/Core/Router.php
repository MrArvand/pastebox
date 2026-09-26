<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

final class Router
{
    /** @var array<int, array{method:string,pattern:string,handler:callable|array{string,string},constraints:array<string,string>}> */
    private array $routes = [];

    /**
     * @param callable|array{string,string} $handler
     * @param array<string, string> $constraints
     */
    public function add(string $method, string $pattern, callable|array $handler, array $constraints = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
            'constraints' => $constraints,
        ];
    }

    /** @param callable|array{string,string} $handler */
    public function get(string $pattern, callable|array $handler, array $constraints = []): void
    {
        $this->add('GET', $pattern, $handler, $constraints);
    }

    /** @param callable|array{string,string} $handler */
    public function post(string $pattern, callable|array $handler, array $constraints = []): void
    {
        $this->add('POST', $pattern, $handler, $constraints);
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $regex = $this->compilePattern($route['pattern'], $route['constraints']);
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            $this->invokeHandler($route['handler'], $request, $params);
            return;
        }

        View::render('errors.404', ['title' => 'صفحه پیدا نشد'], 404);
    }

    /**
     * @param array<string, string> $constraints
     */
    private function compilePattern(string $pattern, array $constraints): string
    {
        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', static function (array $match) use ($constraints): string {
            $name = $match[1];
            $constraint = $constraints[$name] ?? '[^/]+';
            return '(?P<' . $name . '>' . $constraint . ')';
        }, $pattern);

        return '#^' . $regex . '$#';
    }

    /**
     * @param callable|array{string,string} $handler
     * @param array<string,string> $params
     */
    private function invokeHandler(callable|array $handler, Request $request, array $params): void
    {
        if (is_array($handler)) {
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass();
            $callable = Closure::fromCallable([$controller, $method]);
            $callable($request, $params);
            return;
        }

        $handler($request, $params);
    }
}
