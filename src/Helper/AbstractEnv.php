<?php

namespace LTSC\Helper;


abstract class AbstractEnv implements InterfaceHandler
{
    public function __construct() {}

    abstract function env(string $key = null);
}