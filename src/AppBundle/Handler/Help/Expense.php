<?php

namespace AppBundle\Handler\Help;

use AppBundle\Event;
use AppBundle\Handler\AbstractHandler;
use AppBundle\Response\Message;

class Expense extends AbstractHandler
{

    protected $words = [
        'help expense', 'про витрати', 'інфо витрати'
    ];

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;
        $event->setResponse(new Message($this->render(':message/help:expense.md.twig')));
        $event->stopPropagation();
    }
}
