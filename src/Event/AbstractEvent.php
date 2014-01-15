<?php

namespace Event;

abstract class AbstractEvent
{
    protected $postObj;

    protected $response;

    public function __construct($postObj)
    {
        $this->postObj = $postObj;
    }

    public function getUrlResponse($url)
    {
        $urlResponse = json_decode(file_get_contents($url));

        if (!is_object($urlResponse)) {
            throw new \Exception('对不起，数据获取失败，请重试');
        }

        return $urlResponse;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
