<?php

namespace LTSC\Handler\Router\Parser;


use LTSC\Handler\Router\Route;
use LTSC\Helper\AbstractRouteParser;

class UserDefineRouteParser extends AbstractRouteParser
{
    protected $RouteRules = [];

    public function run() {
        $request = $this->core->request();
        $method = $request->method();

        $rules = $this->parserAllRouteRules($method);

        $sName = $request->server('SCRIPT_NAME');
        $rUri = $request->server('REQUEST_URI');
        $uri = trim(str_replace($sName, '', $rUri), '/');
        $uri = explode('/', $uri);

        $uri_count = count($uri);
        $find = null;
        foreach($rules as $ruleObj) {
            $ruleArrs = $ruleObj->rule;
            $handle = $ruleObj->handle;
            foreach($ruleArrs as $ruleArr) {
                if(count($ruleArr) == $uri_count) {
                    $find = $this->isEqual($ruleArr, $uri);
                    if(!is_null($find))
                        $find->handle = $handle;
                }
                if(!is_null($find)) {
                    foreach($ruleArrs as $ruleArr2) {
                        foreach($ruleArr2 as $r) {
                            if(is_array($r)) {
                                $name = $r[0];
                                if(!isset($find->ud[$name]))
                                    $find->ud[$name] = null;
                            }
                        }
                    }
                    return $find;
                }
            }
        }
        return null;
    }

    protected function parserAllRouteRules($httpMethod): array {
        $router = $this->core->router();
        $routes = $router->getRoutes($httpMethod);
        $parser = new RouteParse();
        if(empty($routes))
            return null;
        $rules = [];
        foreach($routes as $route => $handle) {
            $rule = new Route();
            $rule->rule = $parser->parse($route);
            $rule->handle = $handle;
            $rules[] = $rule;
        }
        return $rules;
    }

    protected function isEqual(array $rules, array $uri) {
        $count = count($uri);
        $found = 0;
        $route = new Route();
        $route->ud = [];
        for($i = 0; $i < $count; $i++) {
            $rule = $rules[$i];
            $upart = $uri[$i];
            if(is_string($rule)) {
                //$rule = trim($rule, '/');
                if($rule == $upart) {
                    //$route->ud[$upart] = $upart;
                    $found++;
                }
            } else { //is_array($rule) && count($rule) == 2
                $name = $rule[0];
                $regex = $rule[1];
                $regex = "#$regex#i";
                if(preg_match($regex, $upart) == 1){
                    $route->ud[$name] = $upart;
                    $found++;
                }
            }
        }
        if($found == $count)
            return $route;
        else
            return null;
    }
}