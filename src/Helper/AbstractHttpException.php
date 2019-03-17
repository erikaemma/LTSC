<?php

namespace LTSC\Helper;


abstract class AbstractHttpException extends \Exception implements InterfaceException
{
    abstract public function reponse();
    abstract public static function responseErr($e);

}