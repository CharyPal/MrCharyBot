<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Wallet;
use AppBundle\Event;
use AppBundle\Response\Message;
use Money\Currency;

class Start extends AbstractHandler
{
    protected $words = ['start', 'привіт', 'ola', 'hello', 'здоров'];
    
    /**
     * @param Event $event
     * @throws \Twig_Error
     */
    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;
        $user = $event->getMessageAuthor();
        $wallet = $this->em->getRepository('AppBundle:Wallet')->findOneBy(['account' => $event->getMessageChatId()]);
        if (!$wallet) {
            $wallet = new Wallet;
            $wallet->setAccount($event->getMessageChatId());
            $wallet->setDefaultCurrency(new Currency('USD'));
            $this->em->persist($wallet);
            $this->em->flush();
        }
        
        $event->stopPropagation();
        $event->setResponse(new Message(
            $this->render(':message:start.md.twig', ['user' => $user, 'wallet' => $wallet])
        ));
    }
}
