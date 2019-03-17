<?php

namespace LTSC\Handler;


use LTSC\Core;
use LTSC\Exceptions\HttpException;
use LTSC\Handler\Router\Parser\PathinfoRouteParser;
use LTSC\Handler\Router\Parser\UriRouteParser;
use LTSC\Handler\Router\Parser\UserDefineRouteParser;
use LTSC\Helper\AbstractAction;
use LTSC\Helper\AbstractRouter;


final class RouterHandler extends AbstractRouter
{
    /**
     * @var Core|null
     */
    protected $core = null;

    protected $parsers = [
        'uri' => UriRouteParser::class,
        'pathinfo' => PathinfoRouteParser::class,
        'user-define' => UserDefineRouteParser::class
    ];

    protected $HttpMethod = ['GET', 'POST'];

    protected $routes = ['GET' => [], 'POST' => []];

    public function register(Core $core) {
        $this->core = $core;
        $core->Container()->set('router', $this);
    }

    function get(string $uri, callable $handle): bool {
        if(key_exists($uri, $this->routes['GET']))
            return false;
        $this->routes['GET'][$uri] = $handle;
        return true;
    }

    function post(string $uri, callable $handle): bool {
        if(key_exists($uri, $this->routes['POST']))
            return false;
        $this->routes['POST'][$uri] = $handle;
        return true;
    }

    public function getRoutes(string $httpMethod = null): array {
        if(is_null($httpMethod))
            return $this->routes;
        $httpMethod = strtoupper($httpMethod);
        if(in_array($httpMethod, $this->HttpMethod))
            return $this->routes[$httpMethod];
    }

    public function run() {
        $env = $this->core->env();
        $MvcRoot = fullPath($env->env('ROUTER_ROOT'));
        $requestUri = $this->core->request()->server('REQUEST_URI');
        $route = null;
        $routeMode = null;
        if(strpos($requestUri, '.php?')) {
            $routeMode = 'mvc';
            $parser = new $this->parsers['uri']($this->core);
            $route = $parser->run();
        } else {
            $routeMode = 'ud';
            $parser = new $this->parsers['user-define']($this->core);
            $route = $parser->run();
            if(is_null($route)) {
                $routeMode = 'mvc';
                $parser = new $this->parsers['pathinfo']($this->core);
                $route = $parser->run();
            }
        }

        if($routeMode == 'mvc') {
            $module = ucfirst($route->module);
            $controller = ucfirst($route->controller);
            $action = ucfirst($route->action);
            $classname = "{$module}\\{$controller}";
            $file = $MvcRoot . $module . DIRECTORY_SEPARATOR . $controller . ".php";
            if(!file_exists($file)) {
                throw new HttpException(
                    404,
                    "Page '{$file}' not found."
                );
            }
            require($file);
            if(!class_exists($classname)) {
                throw new HttpException(
                    404,
                    "Class '{$classname}' not found."
                );
            }
            $class = new $classname($this->core);
            if(!($class instanceof AbstractAction)) {
                throw new HttpException(
                    404,
                    "Class '{$classname}' not an AbstractAction class."
                );
            }
            if(!((new \ReflectionObject($class))->hasMethod($action))) {
                throw new HttpException(
                    404,
                    "Class '{$classname}->{$action}' can not be used."
                );
            }
            $class->$action();
        } elseif ($routeMode == 'ud') {
            $parameters = $route->ud;
            $handle = $route->handle;
            $this->core->setResponseData(call_user_func_array($handle, $parameters));
            //$this->app->setResponseData(call_user_func($handle, $parameters));
        }
    }
}