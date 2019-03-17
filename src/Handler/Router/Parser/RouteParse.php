<?php

namespace LTSC\Handler\Router\Parser;


use LTSC\Exceptions\HttpException;

class RouteParse
{
    public function parse(string $route): array {
        $routes = $this->partOptional($route);
        $routeInfo = [];
        foreach($routes as $n => $route) {
            $route = explode('/', $route);
            foreach($route as $uri) {
                if($this->isOptional($uri)) {
                    throw new HttpException(500, "可选路由只能是最后一个：$uri");
                }
                if($this->isStatic($uri)) {
                    $routeInfo[$n][] = $uri;
                } else {
                    $uriInfo = $this->parseDynamicUri($uri);
                    if(is_null($uriInfo)) {
                        throw new HttpException(500, "动态路由解析失败");
                    }
                    $routeInfo[$n][] = [$uriInfo['name'], $uriInfo['regex']];
                }
            }
        }
        return $routeInfo;
    }

    protected function partOptional($route): array {
        $route = trim($route, '/');
        if(substr($route, strlen($route) - 1, 1) != ']')
            return [$route, ''];
        $leftPos = strpos($route, '/[');
        $left = trim(substr($route, 0, $leftPos + 1), '/');
        $option = rtrim(substr($route, $leftPos + 2), ']');
        return [$left, $left . '/' . $option];
    }

    protected function isOptional($uri): bool {
        if(substr($uri, 0, 1) == '[' && substr($uri, strlen($uri) - 1, 1) == ']')
            return true;
        return false;
    }

    protected function isStatic($uri): bool {
        if(substr($uri, 0, 1) == '{' && substr($uri, strlen($uri) - 1, 1) == '}')
            return false;
        return true;
    }

    protected function parseDynamicUri($uri): array {
        $pattern = "/\{(?P<name>[a-zA-Z_][a-zA-Z0-9_-]*)(?::(?P<regex>\S+))*\}/";
        $found = preg_match($pattern, $uri, $matches);
        if($found == 1) {
            $uriInfo['name'] = $matches['name'];
            if(isset($matches['regex']))
                $uriInfo['regex'] = $matches['regex'];
            else
                $uriInfo['regex'] = "[^/]+";
            return $uriInfo;
        }
        return null;
    }
}