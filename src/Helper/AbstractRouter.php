<?php

namespace LTSC\Helper;


abstract class AbstractRouter implements InterfaceHandler
{
    public function __construct() {}

    abstract function get(string $uri, callable $handle): bool;

    abstract function post(string $uri, callable $handle): bool;

    abstract public function getRoutes(string $httpMethod = null): array;

    abstract public function run();
}