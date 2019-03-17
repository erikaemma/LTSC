<?php

namespace LTSC\Handler;


use LTSC\Core;
use LTSC\Helper\AbstractRequest;

final class RequestHandler extends AbstractRequest
{
    protected $_nohtml = false;
    protected $_get = [];
    protected $_post = [];
    protected $_request = [];
    protected $_server = [];
    protected $_files = [];
    protected $_cookie = [];
    protected $_clientIp = '';
    protected $_serverIp = '';
    protected $_method = '';

    public function __construct() {
        $this->_server = $_SERVER;
        $this->_method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
        $this->_serverIp = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        $this->_clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $this->_request = $_REQUEST;
        $this->_get = $_GET;
        $this->_post = $_POST;
        $this->_cookie = $_COOKIE;
        $this->_files = $_FILES;
    }

    public function register(Core $core) {
        $core->Container()->set('request', RequestHandler::class);
        $this->_nohtml = $core->env()->env('REQUEST_NOHTML');
    }

    public function get(string $name, $default = null, bool $checkEmpty = false) {
        if(!key_exists($name, $this->_get))
            return $default;
        $value = $this->_get[$name];
        if($checkEmpty) {
            if(empty($value))
                return $default;
        }
        return $this->nohtml($value);
    }

    public function post(string $name, $default = null, bool $checkEmpty = true) {
        if(!key_exists($name, $this->_post))
            return $default;
        $value = $this->_post[$name];
        if($checkEmpty) {
            if(empty($value))
                return $default;
        }
        return $this->nohtml($value);
    }

    public function request(string $name, $default = null, bool $checkEmpty = true) {
        if(!key_exists($name, $this->_request))
            return $default;
        $value = $this->_request[$name];
        if($checkEmpty) {
            if(empty($value))
                return $default;
        }
        return $this->nohtml($value);
    }

    public function server(string $name, $default = null, bool $checkEmpty = true) {
        if(!key_exists($name, $this->_server))
            return $default;
        $value = $this->_server[$name];
        if($checkEmpty) {
            if(empty($value))
                return $default;
        }
        return $this->nohtml($value);
    }

    public function cookie(string $name, $default = null, bool $checkEmpty = true) {
        if(!key_exists($name, $this->_cookie))
            return $default;
        $value = $this->_cookie[$name];
        if($checkEmpty) {
            if(empty($value))
                return $default;
        }
        return $this->nohtml($value);
    }

    public function files(): array {
        return $this->_files;
    }

    public function clientIp(): string {
        return $this->_clientIp;
    }

    public function serverIp(): string {
        return $this->_serverIp;
    }

    public function method(): string {
        return $this->_method;
    }

    protected function nohtml($value) {
        if($this->_nohtml) {
            if(is_string($value)) {
                return htmlspecialchars($value);
            } elseif(is_array($value)) {
                foreach($value as &$v)
                    $v = $this->nohtml($v);
                return $value;
            } else {
                return $value;
            }
        }
        return $value;
    }
}