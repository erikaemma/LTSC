<?php

namespace LTSC\Handler;


use LTSC\Core;
use LTSC\Helper\AbstractResponse;

final class ResponseHandler extends AbstractResponse
{
    public function register(Core $core){
        $core->Container()->set('response', ResponseHandler::class);
    }

    public function response($response)
    {
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode(
            $response,
            JSON_UNESCAPED_UNICODE)
        );
    }

    public function restSuccess($response)
    {
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode([
            'code' => 200,
            'message' => 'OK',
            'result' => $response
        ], JSON_UNESCAPED_UNICODE)
        );
    }

    public function cliModeSuccess($response)
    {
        var_dump([
            'code' => 200,
            'message' => 'OK',
            'result' => $response
        ]);
    }

    public function restFail($code = 500, $message = 'Internet Server Error', $response)
    {
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode([
            'code' => $code,
            'message' => $message,
            'result' => $response
        ], JSON_UNESCAPED_UNICODE));
    }
}