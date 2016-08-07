<?php

namespace AppBundle\Telegram\Event;

use AppBundle\Entity\Bot;
use AppBundle\ResponseInterface;
use TelegramBot\Api\Types\Message;

class MessageRespond extends MessageReceive
{
    const NAME = 'message.respond';

    /** @var Bot  */
    private $bot;

    public function __construct(Message $message, ResponseInterface $response, Bot $bot)
    {
        parent::__construct($message);
        $this->setResponse($response);
        $this->bot = $bot;
    }

    public function getBot()
    {
        return $this->bot;
    }
}
