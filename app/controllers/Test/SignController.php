<?php

namespace App\Controllers\Test;

use App\Controllers\Controller;
use App\Support\Sign\Md5;
use App\Utils\Log;

class SignController extends Controller
{

    public function verifyAction()
    {
        $params = $this->request->get();
        unset($params['_url']);
        $json = $this->request->getJsonRawBody(true) ?? [];
        $params = array_merge($params, $json);
        $headers = $this->request->getHeaders();
        foreach ($headers as $key => $val) {
            $headers[strtoupper($key)] = $val;
        }

        $params['xappmac'] = $headers['X-APP-MAC'] ?? null;
        $params['xappversion'] = $headers['X-APP-VERSION'] ?? null;
        $params['xapptoken'] = $headers['X-APP-TOKEN'] ?? null;
        $params['xappplatform'] = $headers['X-APP-PLATFORM'] ?? null;
        $params['xappkey'] = $headers['X-APP-KEY'] ?? null;
        $params['xappnonce'] = $headers['X-APP-NONCE'] ?? null; // 0
        $params['xappuuids'] = $headers['X-APP-UUIDS'] ?? null; // 1
        $params['xapptimes'] = $headers['X-APP-TIMES'] ?? null; // 2

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

