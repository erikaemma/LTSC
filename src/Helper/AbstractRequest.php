<?php

namespace LTSC\Helper;


use LTSC\Core;

abstract class AbstractRequest implements InterfaceHandler
{
    public function __construct() {}

    abstract public function get(string $name, $default = null, bool $checkEmpty = true);
    abstract public function post(string $name, $default = null, bool $checkEmpty = true);
    abstract public function request(string $name, $default = null, bool $checkEmpty = true);
    abstract public function server(string $name, $default = null, bool $checkEmpty = true);
    abstract public function files(): array; //return $_FILE directly
    abstract public function cookie(string $name, $default = null, bool $checkEmpty = true);
    abstract public function clientIp(): string;
    abstract public function serverIp(): string;
    abstract public function method(): string;

    public function required($variables) {
        return new RequestValueValidation((array)$variables, $this);
    }
}