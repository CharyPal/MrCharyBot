<?php

namespace AppBundle\Telegram\Event;

use AppBundle\Event;
use AppBundle\ResponseInterface;
use TelegramBot\Api\Types\Message;

class MessageReceive extends Event
{
    const NAME = 'message.receive';

    /** @var Message */
    private $message;

    /**
     * IncomingMessageEvent constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }
    
    /**
     * Get chat where the message has been seen.
     * @return integer
     */
    public function getMessageChatId()
    {
        return $this->getMessage()->getChat()->getId();
    }

    /**
     * @return string
     */
    public function getMessageText()
    {
        return $this->getMessage()->getText();
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return $this->getMessage()->getMessageId();
    }

    /**
     * @return \TelegramBot\Api\Types\User
     */
    public function getMessageAuthor()
    {
        return $this->getMessage()->getFrom();
    }

    /**
     * @return \DateTime
     */
    public function getMessageDate()
    {
        $dateTime = new \DateTime;
        $dateTime->setTimestamp($this->getMessage()->getDate());
        return $dateTime;
    }

    public function setResponse(ResponseInterface $response)
    {
        $response->setFor($this->getMessageChatId());
        return parent::setResponse($response);
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
