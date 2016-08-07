<?php

namespace AppBundle\Handler\Category;

use AppBundle\Event;
use AppBundle\Response\Message;

class Merge extends Rename
{
    protected $regularExpression = "/^[\/]?mergecategory\s(?P<oldTitle>[\S]+)\s(?P<newTitle>[\S]+)$/iu";

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        $event->stopPropagation();
        $wallet = $this->getWallet($event);

        $oldTitle = $this->getOldTitle();
        $oldCategory = $this->em->getRepository('AppBundle:Category')
            ->findOneBy(['title' => $oldTitle, 'wallet' => $wallet]);
        if (!$oldCategory) {

            $event->setResponse(new Message($this->render(
                ':message/category:notfound.md.twig', ['categoryName' => $oldTitle]
            )));
            return;
        }

        $newTitle = $this->getNewTitle();
        $newCategory = $this->em->getRepository('AppBundle:Category')
            ->findOneBy(['title' => $newTitle, 'wallet' => $wallet]);

        if (!$newCategory) {
            $event->setResponse(new Message($this->render(
                ':message/category:notfound.md.twig', ['categoryName' => $newTitle]
            )));
            return;
        }

        $expenses = $this->em->getRepository('AppBundle:Expense')
            ->findBy(['category' => $oldCategory]);
        foreach ($expenses as $expense) {
            $expense->setCategory($newCategory);
            $this->em->persist($expense);
        }
        $this->em->remove($oldCategory);
        $this->em->flush();
        $event->setResponse(new Message($this->render(
            ':message/category:merged.md.twig', ['oldCategory' => $oldCategory, 'newCategory' => $newCategory]
        )));
    }
}
