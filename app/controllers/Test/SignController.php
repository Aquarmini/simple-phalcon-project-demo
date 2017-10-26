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
        $params['xappmac'] = $this->request->getHeader('X-APP-MAC');
        $params['xappversion'] = $this->request->getHeader('X-APP-VERSION');
        $params['xapptoken'] = $this->request->getHeader('X-APP-TOKEN');
        $params['xappplatform'] = $this->request->getHeader('X-APP-PLATFORM');
        $params['xappkey'] = $this->request->getHeader('X-APP-KEY');
        $params['xappnonce'] = $this->request->getHeader('X-APP-NONCE'); // 0
        $params['xappuuids'] = $this->request->getHeader('X-APP-UUIDS'); // 1
        $params['xapptimes'] = $this->request->getHeader('X-APP-TIMES'); // 2

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

