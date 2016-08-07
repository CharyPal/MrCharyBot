<?php

namespace AppBundle\Handler;

use AppBundle\Event;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

abstract class AbstractHandler
{
    /** @var TwigEngine */
    protected $twig;

    /** @var LoggerInterface */
    protected $logger;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var array  */
    protected $words = [];

    /** @var array Matched parts from regexp */
    protected $matches = [];

    protected $regularExpression = '/^[\/]?(:words)/i';

    public function __construct(TwigEngine $twig, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->logger = $logger;
        $this->em = $em;
    }

    public function setWords(array $words)
    {
        $this->words = $words;
    }

    abstract public function onMessageReceive(Event $event);

    /**
     * Check if this handler should be the one to handle the message.
     * 
     * @param Event $event
     * @return bool
     */
    protected function shouldHandle(Event $event)
    {
        return (bool) preg_match($this->getRegularExpression(), $event->getMessageText(), $this->matches);
    }

    /**
     * @return string
     */
    protected function getRegularExpression()
    {
        $words = implode('|', $this->words);
        return str_replace(':words', $words, $this->regularExpression);
    }

    /**
     * Find wallet for message
     * @param Event $event
     * @return \AppBundle\Entity\Wallet
     */
    protected function getWallet(Event $event)
    {
        $wallet =  $this->em->getRepository('AppBundle:Wallet')
            ->findOneBy(['account' => $event->getMessageChatId()]);
        if (!$wallet)
            throw new \LogicException("Wallet not found for {$event->getMessageChatId()}");
        return $wallet;
    }

    /**
     * Render the template with data
     * @param $template
     * @param array $data
     * @return string
     * @throws \Twig_Error
     */
    protected function render($template, array $data = [])
    {
        return $this->twig->render($template, $data);
    }
}
