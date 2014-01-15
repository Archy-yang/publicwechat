<?php

namespace Response;

class TextMsgResponse extends AbstractResponse
{
    protected $content;

    public function __construct($postObj)
    {
        parent::__construct($postObj);

        $this->msgType = 'text';
    }

    public function setXMLContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function textMsg()
    {
        return include_once(__DIR__.'/../views/text.php.xml');
    }
}
