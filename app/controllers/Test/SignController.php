<?php

namespace App\Controllers\Test;

use App\Controllers\Controller;
use App\Support\Sign\Md5;
use App\Utils\Log;

class SignController extends Controller
{
    public $headers = [
        'X-APP-MAC',
        'X-APP-VERSION',
        'X-APP-TOKEN',
        'X-APP-PLATFORM',
        'X-APP-KEY',
        'X-APP-NONCE',
        'X-APP-UUIDS',
        'X-APP-TIMES'
    ];

    public function headerToServer($header)
    {
        return 'HTTP_' . str_replace('-', '_', $header);
    }

    public function verifyAction()
    {
        $params = $this->request->get();
        unset($params['_url']);
        $json = $this->request->getJsonRawBody(true) ?? [];
        $params = array_merge($params, $json);

        foreach ($this->headers as $header) {
            if ($this->request->hasServer($this->headerToServer($header))) {
                $key = strtolower(str_replace('-', '', $header));
                $params[$key] = $this->request->getHeader($header);
            }
        }

        $sign = $this->request->getHeader('X-APP-SIGN');

        if (empty($sign) || strlen($sign) < 20) {
            return $this->response->setJsonContent([
                'result' => false,
                'message' => '签名不合法'
            ]);
        }

        Log::info(json_encode($params));
        Log::info($sign);
        $result = (new Md5())->verify($params, $sign);

        return $this->response->setJsonContent([
            'result' => $result,
        ]);
    }


}

