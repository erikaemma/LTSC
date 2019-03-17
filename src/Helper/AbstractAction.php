<?php

namespace LTSC\Helper;


use LTSC\Core;

class AbstractAction
{
    protected $core = null;

    public function __construct(Core $core) {
        $this->core = $core;
    }
}