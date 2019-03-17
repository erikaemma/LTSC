<?php

namespace LTSC\Handler;


use LTSC\Core;
use LTSC\Exceptions\HttpException;
use LTSC\Helper\InterfaceHandler;

final class ErrorHandler implements InterfaceHandler
{
    protected $info = [];

    public function register(Core $core) {
        error_reporting(0);
        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'shutdown']);
    }

    public function shutdown() {
        $error = error_get_last();
        if(empty($error))return;
        $this->info = [
            'type' => $error['type'],
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ];
        $this->end();
    }

    public function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine, $errorContext) {
        $this->info = [
            'type' => $errorNumber,
            'message' => $errorMessage,
            'file' => $errorFile,
            'line' => $errorLine,
            'context' => $errorContext
        ];
        $this->end();
    }

    protected function end() {
        //switch(mode)
        HttpException::responseErr($this->info);
    }
}