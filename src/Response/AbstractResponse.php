<?php

namespace Response;

abstract class AbstractResponse implements ResponseInterface
{
    protected $userName;

    protected $hostName;

    protected $msgType;

    public function __construct($postObj, $msgType)
    {
        $this->userName = $postObj->FromUserName;
        $this->hostName = $postObj->ToUserName;
        $this->msgType = $msgType;
    }

    public function send()
    {
        $func = $this->msgType.'Msg';

        return $this->$func();
    }
}
