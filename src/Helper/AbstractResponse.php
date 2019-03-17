<?php

namespace LTSC\Helper;


abstract class AbstractResponse implements InterfaceHandler
{
    public function __construct() {}

    abstract public function response($response);

    abstract public function restSuccess($response);

    abstract public function cliModeSuccess($response);

    abstract public function restFail($code = 500, $message = 'Internet Server Error', $response);
}