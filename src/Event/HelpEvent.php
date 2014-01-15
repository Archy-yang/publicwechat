<?php

namespace Event;

use Response\TextMsgResponse;

class HelpEvent extends AbstractEvent
{
    protected $content;

    protected $conf;

    public function __construct(array $conf, $postObj)
    {
        parent::__construct($postObj);

        $this->conf = $conf;
    }

    public function setContent()
    {
        $this->content = $this->conf['helpContent'];

        return $this;
    }

    public function setResponse()
    {
        $response = new TextMsgResponse($this->postObj);

        $response->setXMLContent($this->content);

        $this->response = $response->send();

        return $this;
    }
}
