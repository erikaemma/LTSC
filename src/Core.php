<?php

namespace LTSC;


use LTSC\Exceptions\HttpException;
use LTSC\Helper\AbstractEnv;
use LTSC\Helper\AbstractFilter;
use LTSC\Helper\AbstractRequest;
use LTSC\Helper\AbstractResponse;
use LTSC\Helper\AbstractRouter;
use LTSC\Helper\InterfaceHandler;
use LTSC\Helper\InterfaceContainer;

class Core
{
    protected $_container = null;

    protected $_responseData;

    public function __construct() {
        $this->_container = new Container();
    }

    public function Container(): InterfaceContainer {
        return $this->_container;
    }

    public function handler($handle) {
        if(is_callable($handle)) {
            $handler = $handle();
            if($handler instanceof InterfaceHandler)
                $handler->register($this);
        } elseif($handle instanceof InterfaceHandler) {
            $handle->register($this);
        } elseif(is_string($handle) && class_exists($handle)) {
            $handler = new $handle();
            if($handler instanceof InterfaceHandler)
                $handler->register($this);
        } else {
            return false;
        }
        return true;
    }

    public function env(): AbstractEnv {
        return $this->_container->get('env');
    }

    public function request() : AbstractRequest {
        return $this->_container->get('request');
    }

    public function response(): AbstractResponse {
        return $this->_container->get('response');
    }

    public function router(): AbstractRouter {
        return $this->_container->get('router');
    }

    public function run(array $filters = []) {
        foreach($filters as $filter) {
            if($filter instanceof AbstractFilter) {
                if(!$filter->filter($this->request())) {
                    throw new HttpException(
                        500,
                        "Filter failed"
                    );
                }
            }
        }
        $this->router()->run();
        register_shutdown_function([$this, 'responseShutdown']);
    }

    public function responseShutdown() {
        $this->response()->response($this->_responseData);
    }

    public function getResponseData()
    {
        return $this->_responseData;
    }

    public function setResponseData($responseData): void
    {
        $this->_responseData = $responseData;
    }
}