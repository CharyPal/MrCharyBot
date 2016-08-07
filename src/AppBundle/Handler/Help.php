<?php

namespace AppBundle\Handler;

use AppBundle\Event;
use AppBundle\Response\Message;

class Help extends AbstractHandler
{
    protected $regularExpression = '/^[\/]?(help|допоможи|інфо)$/i';

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;
        
        $event->stopPropagation();
        $event->setResponse(new Message($this->render(':message:help.md.twig')));
    }
}
