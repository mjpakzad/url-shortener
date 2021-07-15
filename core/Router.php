<?php
namespace Core;

use App\Controllers\Controller;

class Router
{
    private $routes     = [];
    private $namespace  = 'App\Controllers';
    private $uri, $httpHost, $segments;

    public function __construct()
    {
        $this->uri          = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->httpHost     = 'http://' . parse_url($_SERVER['HTTP_HOST'], PHP_URL_PATH);
        $this->segments     = explode('/', $this->uri);
    }

    public function handler()
    {
        $url = config('app', 'url');
        $partUrl = $this->httpHost;
        foreach ($this->segments as $segment) {
            $partUrl .= $segment;
            if (strpos($url, $partUrl) === false) {
                switch ($segment) {
                    case 'api':
                        require_once 'routes/api.php';
                        break;
                    default:
                        require_once 'routes/web.php';
                }
                break;
            }
            $partUrl .= '/';
        }
    }

    public function getMethod()
    {
        return parse_url($_SERVER['REQUEST_METHOD'], PHP_URL_PATH);
    }

    private function register($method, $uri, $action)
    {
        $uris = explode('/', $uri);
        $uriPieces  = '';
        $parameters = [];
        foreach ($uris as $uri) {
            if(strpos($uri, '{') !== false) {
                $parameters = [rtrim(ltrim($uri, '{'), '}')];
                break;
            }
            $uriPieces .= $uri . '/';
        }
        $uriPieces = rtrim($uriPieces, '/');
        list($controller, $action) = explode('@', $action);
        $this->routes[$method][$uriPieces] = [
            'parameters'    => $parameters,
            'controller'    => $controller,
            'action'        => $action,
        ];
    }

    public function get($uri, $action)
    {
        $this->register('get', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->register('post', $uri, $action);
    }

    public function patch($uri, $action)
    {
        $this->register('patch', $uri, $action);
    }

    public function delete($uri, $action)
    {
        $this->register('delete', $uri, $action);
    }

    public function resolver()
    {
        $method = $this->getMethod();
        $segments = $this->segments;
        unset($segments[0], $segments[1], $segments[2]);
        $countSegments = count($segments);
        $segmentIndex = '';
        $segmentParameters = [];
        for($i = $countSegments; $i >= 1; $i--) {
            if(in_array(implode('/', $segments), array_keys($this->routes[strtolower($method)]))) {
                $segmentIndex = implode('/', $segments);
                break;
            }
            $segmentParameters[] = end($segments);
            array_pop($segments);
        }
        if ($segmentIndex != '') {
            if(count($this->routes[strtolower($method)][$segmentIndex]['parameters']) != count($segmentParameters)) {
                $controller = new Controller();
                $controller->notFound();
                return;
            }

            $seg = $this->routes[strtolower($method)][$segmentIndex];
            $con = '\\' . $this->namespace . '\\' . $seg['controller'];
            $act = $seg['action'];
            $controller = new $con();
            $controller->$act();
            return;
        }
        if (isset($segmentParameters)) {
            $seg = $this->routes[strtolower($method)][$segmentIndex];
            $con = '\\' . $this->namespace . '\\' . $seg['controller'];
            $act = $seg['action'];
            $controller = new $con();
            $controller->$act(end($segmentParameters));
            return;
        }
        $controller = new Controller();
        $controller->notFound();
    }
}