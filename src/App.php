<?php

namespace LTSC;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use LTSC\Handler\EnvHandler;
use LTSC\Handler\ErrorHandler;
use LTSC\Handler\ExceptionHandler;
use LTSC\Handler\RequestHandler;
use LTSC\Handler\ResponseHandler;
use LTSC\Handler\RouterHandler;
use LTSC\Helper\AbstractRequest;
use LTSC\Helper\AbstractResponse;
use LTSC\Helper\AbstractRouter;

class App {

    protected $core = null;

    public function __construct(string $root, string $env = null) {
        $this->core = new Core();
        $this->init($root, $env);
    }

    public function init($root, $file) {
        $core = $this->core;
        $core->handler(function() use($root, $file) {
            $env = new EnvHandler();
            $env->support(['array', 'putenv']);
            $env->build($root, $file);
            $env->load();
            return $env;
        });
        $core->handler(function () {
            return new RequestHandler();
        });
        $core->handler(function () {
            return new ResponseHandler();
        });
        $core->handler(ErrorHandler::class);
        $core->handler(ExceptionHandler::class);
        $core->handler(function() {
            return new RouterHandler();
        });
    }

    public function getRouter(): AbstractRouter {
        return $this->core->router();
    }

    public function getRequest(): AbstractRequest {
        return $this->core->request();
    }

    public function getResponse(): AbstractResponse {
        return $this->core->response();
    }

    public function run(array $filters = []) {
        $this->core->run($filters);
    }
}