<?php

namespace Controller;

use Event;

class DispacherController
{
    protected $postObj;

    protected $conf;

    public function __construct(array $conf, $postObj)
    {
        $this->postObj = $postObj;
        $this->conf = $conf;
    }

    public function getEvent()
    {
        $userMsg = trim($this->postObj->Content);

        foreach ($this->conf['action'] as $key => $val) {
            if ($val === $userMsg) {
                $eventClass = 'Event\\'.ucwords($key).'Event';

                return new $eventClass($this->conf, $this->postObj);
            }
        }

        return new Event\HelpEvent($this->conf, $this->postObj);
    }
}
