<?php

namespace LTSC\Handler\Router\Parser;


use LTSC\Handler\Router\Route;
use LTSC\Helper\AbstractRouteParser;

class PathinfoRouteParser extends AbstractRouteParser
{
    public function run() {
        $request = $this->core->request();
        $env = $this->core->env();
        $sName = $request->server('SCRIPT_NAME');
        $rUri = $request->server('REQUEST_URI');
        $uri = trim(str_replace($sName, '', $rUri), '/');
        if($uri == '') {
            $three = 0;
        } else {
            if(strpos($uri, '?')) {
                $uri = explode('/', $uri);
                unset($uri[count($uri) - 1]);
                array_values($uri);
            } else {
                $uri = explode('/', $uri);
            }
            $three = count($uri);
        }
        $route = new Route();
        switch($three) {
            case 3:
                $route->module = $uri[0];
                $route->controller = $uri[1];
                $route->action = $uri[2];
                break;
            case 2:
                $route->module = $env->env('ROUTER_DEFAULT_MODUEL');
                $route->controller = $uri[0];
                $route->action = $uri[1];
                break;
            case 1:
                $route->module = $env->env('ROUTER_DEFAULT_MODUEL');
                $route->controller = $env->env('ROUTER_DEFAULT_CONTROLLER');
                $route->action = $uri[0];
                break;
            default:
                $route->module = $env->env('ROUTER_DEFAULT_MODUEL');
                $route->controller = $env->env('ROUTER_DEFAULT_CONTROLLER');
                $route->action = $env->env('ROUTER_DEFAULT_ACTION');
                break;
        }
        return $route;
    }
}