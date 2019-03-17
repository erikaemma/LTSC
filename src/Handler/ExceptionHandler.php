<?php

namespace LTSC\Handler;


use LTSC\Core;
use LTSC\Exceptions\HttpException;
use LTSC\Helper\InterfaceHandler;

final class ExceptionHandler implements InterfaceHandler
{
    protected $info;

    public function register(Core $core) {
        set_exception_handler([$this, 'exceptionHandler']);
    }

    public function exceptionHandler($exception) {
        $this->info = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
            'previous' => $exception->getPrevious()
        ];
        $this->end();
    }

    protected function end() {
        //switch(mode)
        HttpException::responseErr($this->info);
    }
}