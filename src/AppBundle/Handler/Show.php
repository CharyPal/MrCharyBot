<?php

namespace AppBundle\Handler;

use AppBundle\Event;
use AppBundle\Response\Message;


class Show extends AbstractHandler
{
    protected $words = ['show'];
    
    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        $event->stopPropagation();
        $message = new Message($this->render(':message:show.md.twig'));
        $message->setDisableNotification(false);
        $event->setResponse($message);
    }
}
