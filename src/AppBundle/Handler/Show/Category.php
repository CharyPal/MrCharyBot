<?php

namespace AppBundle\Handler\Show;

use AppBundle\Event;
use AppBundle\Handler\AbstractHandler;
use AppBundle\Response\Message;

class Category extends AbstractHandler
{

    protected $words = [
        'show category', 'show categories', 'show cats'
    ];

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        $wallet = $this->getWallet($event);
        $categories = $this->em->getRepository('AppBundle:Category')
            ->findBy(['wallet' => $wallet], ['title' => 'asc']);
        $event->stopPropagation();

        $event->setResponse(new Message(
            $this->render(':message/show:category.md.twig', ['categories' => $categories])
        ));
    }

}
