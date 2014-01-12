<?php

namespace Response;

class TextMsgResponse extends AbstractResponse
{
    protected $content;

    public function setContents($content)
    {
        $this->content = $content;

        return $this;
    }

    public function textMsg()
    {
        return include_once(__DIR__.'/../views/text.xml');
    }
}
