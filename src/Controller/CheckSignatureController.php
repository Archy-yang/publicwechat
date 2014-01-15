<?php

namespace Controller;

class CheckSignatureController
{
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function checkSignature(array $get)
    {
        $signature = $get['signature'];
        $timeStamp = $get['timestamp'];
        $nonce = $get['nonce'];

        $token = $this->token;
        $tmpArr = array($token, $timeStamp, $nonce);
        sort($tmpArr);
        $tmpStr = sha1(implode($tmpArr));

        if ($tmpStr !== $signature) {
            throw new \Exception('userKeyword is not eq signature!');
        }

        return true;
    }

    public function valid(array $get)
    {
        try {
            $this->checkSignature($get);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }

        return $get['echostr'];
    }
}
