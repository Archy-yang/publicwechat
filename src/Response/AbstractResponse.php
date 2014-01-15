<?php

namespace Response;

abstract class AbstractResponse implements ResponseInterface
{
    protected $userName;

    protected $hostName;

    protected $msgType;

    public function __construct($postObj)
    {
        $this->userName = $postObj->FromUserName;
        $this->hostName = $postObj->ToUserName;
    }

    public function send()
    {
        $func = $this->msgType.'Msg';

        return $this->$func();
    }
}
