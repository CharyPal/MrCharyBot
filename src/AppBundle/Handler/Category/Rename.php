<?php

namespace AppBundle\Handler\Category;

use AppBundle\Event;
use AppBundle\Handler\AbstractHandler;
use AppBundle\Response\Message;

class Rename extends AbstractHandler
{
    protected $regularExpression = '/^[\/]?setcategoryname\s(?P<oldTitle>[\S]+)\s(?P<newTitle>[\S]+)$/iu';

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return $this;

        $event->stopPropagation();
        $wallet = $this->getWallet($event);
        $oldCategoryName = $this->getOldTitle();
        $category = $this->em->getRepository('AppBundle:Category')
            ->findOneBy(['title' => $oldCategoryName, 'wallet' => $wallet]);
        if (is_null($category)) {
            $event->setResponse(new Message($this->twig->render(
                ':message/category:notfound.md.twig',
                ['categoryName' => $oldCategoryName]
            )));
            return;
        }

        $newTitle = $this->getNewTitle();
        $newCategory = $this->em->getRepository('AppBundle:Category')
            ->findOneBy(['title' => $newTitle, 'wallet' => $wallet]);
        if (!is_null($newCategory)) {
            $event->setResponse(new Message(
                $event->getMessageChatId(),
                $this->render(':message/category:exists.md.twig', ['categoryName' => $newTitle])
            ));
            return;
        }

        $category->setTitle($newTitle);
        $this->em->persist($category);
        $event->setResponse(new Message($this->render(
            ':message/category:rename.md.twig',
            ['oldName' => $oldCategoryName, 'category' => $category]
        )));
        $this->em->flush();
    }

    /**
     * @return string
     */
    protected function getOldTitle()
    {
        return $this->matches['oldTitle'];
    }

    /**
     * @return string
     */
    protected function getNewTitle()
    {
        return $this->matches['newTitle'];
    }

}
