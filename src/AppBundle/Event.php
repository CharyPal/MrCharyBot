<?php

namespace AppBundle;

use AppBundle\Entity\Bot;
use Symfony\Component\EventDispatcher\Event as BaseEvent;

abstract class Event extends BaseEvent
{

    abstract public function getMessageChatId();

    abstract public function getMessageText();

    abstract public function getMessageDate();
    
    abstract public function getMessageAuthor();

    abstract public function getMessageId();

    /** @var ResponseInterface */
    protected $response;

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response instanceof ResponseInterface;
    }
}
