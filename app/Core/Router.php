<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

/**
 * Small router: maps "METHOD + path pattern" to a controller method or closure.
 *
 *   $router->get('/products/{id}', [ProductController::class, 'show']);
 *
 * Handlers receive (Request $request, array $params) and return a Response.
 */
final class Router
{
    /** @var list<array{method:string,regex:string,handler:array{0:class-string,1:string}|Closure}> */
    private array $routes = [];

    /** @param array{0:class-string,1:string}|Closure $handler */
    public function get(string $pattern, array|Closure $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /** @param array{0:class-string,1:string}|Closure $handler */
    public function post(string $pattern, array|Closure $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    /** @param array{0:class-string,1:string}|Closure $handler */
    private function add(string $method, string $pattern, array|Closure $handler): void
    {
        $pattern = '/' . trim($pattern, '/');
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);

        $this->routes[] = [
            'method'  => $method,
            'regex'   => '#^' . $regex . '$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $lookupMethod = $method === 'HEAD' ? 'GET' : $method;
        $allowed = [];

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $request->path(), $matches)) {
                continue;
            }
            if ($route['method'] !== $lookupMethod) {
                $allowed[] = $route['method'];
                continue;
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $this->invoke($route['handler'], $request, $params);
        }

        if ($allowed !== []) {
            return Response::html(
                View::render('errors/405', ['title' => 'Method not allowed']),
                405
            )->withHeader('Allow', implode(', ', array_unique($allowed)));
        }

        return Response::html(View::render('errors/404', ['title' => 'Page not found']), 404);
    }

    /**
     * @param array{0:class-string,1:string}|Closure $handler
     * @param array<string,string> $params
     */
    private function invoke(array|Closure $handler, Request $request, array $params): Response
    {
        $result = $handler instanceof Closure
            ? $handler($request, $params)
            : (new $handler[0]())->{$handler[1]}($request, $params);

        if (!$result instanceof Response) {
            throw new RuntimeException('Route handlers must return a Response object.');
        }
        return $result;
    }
}
