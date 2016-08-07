<?php

namespace AppBundle\Handler\Help;

use AppBundle\Event;
use AppBundle\Handler\AbstractHandler;
use AppBundle\Response\Message;
use Money\Currency as MoneyCurrency;

class CurrencyHelp extends AbstractHandler
{
    protected $words = [
        'help currenc', 'інфо валют'
    ];

    protected $regularExpression = '/^[\/]?(:words).*$/i';

    /** @var array  */
    private $currencies = [];

    public function setCurrencies(array $currencies)
    {
        foreach ($currencies as $key => $name) {
            $this->currencies[$name] = new MoneyCurrency($key);
        }
    }

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        $event->stopPropagation();

        $message = new Message($this->render(
            ':message/help:currency.md.twig', ['currencies' => $this->currencies]
        ));
        $this->logger->debug('Message length: ', ['length' => strlen($message->getContent())]);
        $event->setResponse($message);
    }
}
