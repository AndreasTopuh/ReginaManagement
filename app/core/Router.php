<?php

class Router
{
    private $routes = [];
    private $middlewares = [];

    public function get($pattern, $callback)
    {
        $this->addRoute('GET', $pattern, $callback);
    }

    public function post($pattern, $callback)
    {
        $this->addRoute('POST', $pattern, $callback);
    }

    public function put($pattern, $callback)
    {
        $this->addRoute('PUT', $pattern, $callback);
    }

    public function delete($pattern, $callback)
    {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    private function addRoute($method, $pattern, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Remove base path if in subdirectory  
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = rtrim(dirname($scriptName), '/');

        // Fix: Ensure complete base path removal
        if ($basePath && $basePath !== '.' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }

        // Ensure we start with /
        $requestUri = '/' . ltrim($requestUri, '/');

        // If accessing root, redirect to login check
        if ($requestUri === '/') {
            $requestUri = '/';
        }

        // Handle method override for forms
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertPatternToRegex($route['pattern']);

            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match

                try {
                    $this->callAction($route['callback'], $matches);
                    return;
                } catch (Exception $e) {
                    error_log("Router error: " . $e->getMessage());
                    http_response_code(500);
                    echo "500 - Internal Server Error";
                    if (APP_DEBUG) {
                        echo "<br>Error: " . $e->getMessage();
                        echo "<br>Trace: " . $e->getTraceAsString();
                    }
                    return;
                }
            }
        }

        // 404 Not Found
        $this->handleNotFound();
    }

    private function convertPatternToRegex($pattern)
    {
        // Convert {param} to regex group
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }

    private function callAction($callback, $params)
    {
        if (is_string($callback)) {
            list($controllerName, $methodName) = explode('@', $callback);

            // Load controller
            $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller $controllerName not found at $controllerFile");
            }

            if (!class_exists($controllerName)) {
                require_once $controllerFile;
            }

            $controller = new $controllerName();

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method $methodName not found in $controllerName");
            }

            call_user_func_array([$controller, $methodName], $params);
        } elseif (is_callable($callback)) {
            call_user_func_array($callback, $params);
        }
    }

    private function handleNotFound()
    {
        http_response_code(404);

        // Try to load 404 view
        $notFoundView = APP_PATH . '/views/errors/404.php';
        if (file_exists($notFoundView)) {
            include $notFoundView;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The page you are looking for could not be found.</p>";
        }
    }

    public function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header('Location: ' . BASE_URL . $url);
        exit;
    }
}
