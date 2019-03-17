<?php

namespace LTSC\Exceptions;


use LTSC\Helper\AbstractHttpException;

class HttpException extends AbstractHttpException
{
    private static $hadException = false;

    private $httpCode = [
        // 缺少参数或者必传参数为空
        400 => 'Bad Request',
        // 没有访问权限
        403 => 'Forbidden',
        // 访问的资源不存在
        404 => 'Not Found',
        // 代码错误
        500 => 'Internet Server Error',
        // Remote Service error
        503 => 'Service Unavailable'
    ];

    public function __construct($code = 200, $extra = '') {
        $this->code = $code;
        if (empty($extra)) {
            $this->message = $this->httpCode[$code];
            return;
        }
        $this->message = $extra;
    }

    public function reponse() {
        $data = [
            '__coreError' => [
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
                'infomations' => [
                    'file' => $this->getFile(),
                    'line' => $this->getLine(),
                    'trace' => $this->getTrace(),
                ]
            ]
        ];

        //log here

        register_shutdown_function(function () use ($data) {
            header('Content-Type:Application/json; Charset=utf-8');
            die(json_encode($data, JSON_UNESCAPED_UNICODE));
        });
    }

    public static function responseErr($e) {
        if (self::$hadException) {
            //log here
            return;
        }

        self::$hadException = true;

        $data = [
            '__coreError' => [
                'code' => 500,
                'message' => $e['message'],
                'infomations' => [
                    'file' => $e['file'],
                    'line' => $e['line'],
                ]
            ]
        ];

        //log here

        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}