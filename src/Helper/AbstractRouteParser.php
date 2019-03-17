<?php

namespace LTSC\Helper;


use LTSC\Core;

abstract class AbstractRouteParser
{
    protected $core = null;

    public function __construct(Core $core) {
        $this->core = $core;
    }

    abstract public function run();
}