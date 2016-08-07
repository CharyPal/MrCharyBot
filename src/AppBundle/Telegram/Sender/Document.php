<?php

namespace AppBundle\Telegram\Sender;

use AppBundle\Telegram\Event\MessageRespond;

class Document
{
    /**
     * @param MessageRespond $event
     */
    public function onMessageRespond(MessageRespond $event)
    {
        if ($event->getResponse() instanceof \AppBundle\Response\Document == false)
            return;
        $event->stopPropagation();

        /** @var \AppBundle\Response\Document $response */
        $response = $event->getResponse();

        $file = $this->getCurlFile($response);

        $event->getBot()->getApi()->sendDocument($event->getMessageChatId(), $file);
    }

    /**
     * @param \AppBundle\Response\Document $response
     * @return \CURLFile
     */
    private function getCurlFile(\AppBundle\Response\Document $response)
    {
        return new \CURLFile($response->getPath(), $response->getMimeType(), $response->getFile());
    }
}
