<?php

namespace LTSC\Handler\Router\Parser;


use LTSC\Handler\Router\Route;
use LTSC\Helper\AbstractRouteParser;

class UriRouteParser extends AbstractRouteParser
{
    public function run() {
        $request = $this->core->request();
        $env = $this->core->env();
        $route = new Route();
        if(!is_null($request->request('module')))
            $route->module = $request->request('module');
        else
            $route->module = $env->env('ROUTER_DEFAULT_MODUEL');
        if(!is_null($request->request('controller')))
            $route->controller = $request->request('controller');
        else
            $route->controller = $env->env('ROUTER_DEFAULT_CONTROLLER');
        if(!is_null($request->request('action')))
            $route->action = $request->request('action');
        else
            $route->action = $env->env('ROUTER_DEFAULT_ACTION');
        return $route;
    }
}