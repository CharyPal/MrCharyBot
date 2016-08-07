<?php

namespace AppBundle\Telegram;

use AppBundle\Entity\Bot;
use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Telegram\Event\MessageRespond;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

class UpdateHandler
{
    /** @var Bot  */
    private $bot;

    /** @var EventDispatcherInterface  */
    private $eventDispatcher;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * @param Bot $bot
     * @return $this
     */
    public function setBot(Bot $bot)
    {
        $this->bot = $bot;
        return $this;
    }

    /**
     * @param Update $update
     */
    public function handle(Update $update)
    {
        if (is_null($this->bot))
            throw new \LogicException("Please set the bot before using handle.");

        /** @var Message $message */
        $message = $update->getMessage();

        $messageReceive = new MessageReceive($message);
        $this->eventDispatcher->dispatch($messageReceive::NAME, $messageReceive);

        if (!$messageReceive->hasResponse())
            return;

        $respondEvent = new MessageRespond($messageReceive->getMessage(), $messageReceive->getResponse(), $this->bot);
        
        $this->eventDispatcher->dispatch($respondEvent::NAME, $respondEvent);
    }
}
