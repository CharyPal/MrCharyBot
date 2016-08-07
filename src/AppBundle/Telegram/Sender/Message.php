<?php

namespace AppBundle\Telegram\Sender;

use AppBundle\Telegram\Event\MessageRespond;

/**
 * Class Message
 * @package AppBundle\Telegram\Sender
 * Handle the response delivery via telegram api.
 */
class Message
{
    /**
     * @param MessageRespond $event
     */
    public function onMessageRespond(MessageRespond $event)
    {
        if (($event->getResponse() instanceof \AppBundle\Response\Message) == false)
            return;
        $event->stopPropagation();
        /** @var \AppBundle\Response\Message $response */
        $response = $event->getResponse();
        $event->getBot()->getApi()->sendMessage(
            $event->getMessageChatId(),
            $response->getContent(),
            $response->getParseMode(),
            true,
            $response->getReplyTo()
        );
    }
}
