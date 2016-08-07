<?php

namespace AppBundle\Handler;

use AppBundle\Event;
use AppBundle\Response\Message;

class Unknown extends AbstractHandler
{
    public function onMessageReceive(Event $event)
    {
        $this->logger->debug("I am the bastard that runs every time");
        $user = $event->getMessageAuthor();
        $event->setResponse(new Message(
            $this->render(':message:didntgetit.md.twig', ['user' => $user]),
            $event->getMessageId()
        ));
    }

    public function shouldHandle(Event $event)
    {
        return true;
    }
}
