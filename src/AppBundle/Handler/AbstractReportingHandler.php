<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Category;
use AppBundle\Entity\ReportingEntity;
use AppBundle\Entity\Wallet;
use AppBundle\Event;
use AppBundle\MoneyHelper;
use AppBundle\Response\Message;

abstract class AbstractReportingHandler extends AbstractHandler
{
    /** @var string  */
    protected $regularExpression = '/^[\/]?(:words)\s?(:money)\s?(:category)$/iu';

    /** @var string  */
    protected $categoryRegex = '(?P<category>\w+)?';

    /** @var  MoneyHelper */
    protected $moneyParser;

    public function setMoneyParser(MoneyHelper $parser)
    {
        $this->moneyParser = $parser;
    }

    /**
     * @return ReportingEntity
     */
    abstract protected function getReportingEntity();

    /**
     * @return string
     */
    abstract protected function getReportingMessageTemplate();

    /**
     * @param Event $event
     * @throws \Twig_Error
     */
    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        try {
            $wallet = $this->getWallet($event);

            $this->moneyParser->setDefaultCurrency($wallet->getDefaultCurrency());
            $money = $this->moneyParser->parse($this->matches);
            $category = $this->findCategory($wallet, $this->getCategoryName());

            $reportingEntity = $this->getReportingEntity();
            $reportingEntity->setWallet($wallet);
            $reportingEntity->setCategory($category);
            $reportingEntity->setAmount($money);
            $this->em->persist($reportingEntity);
            $this->em->flush();

            $event->stopPropagation();
            $message = new Message(
                $this->render($this->getReportingMessageTemplate(), ['money' => $money, 'category' => $category]),
                $event->getMessageId()
            );
            $message->setDisableNotification(true);

            $event->setResponse($message);

        } catch (\LogicException $e) {
            $message = new Message(
                $this->render($this->twig->render(':message:error.md.twig', ['e' => $e])),
                $event->getMessageId()
            );
            $message->setReplyTo($event->getMessageId());
            $message->setDisableNotification(true);
            $event->setResponse($message);

            $this->logger->error("Logic exception happened when adding data", ['exception' => $e]);
        }
    }

    /**
     * Get category from message
     * @return string
     */
    protected function getCategoryName()
    {
        if (array_key_exists('category', $this->matches))
            return $this->matches['category'];
        return 'uncategorized';
    }

    /**
     * @param Wallet $wallet
     * @param $title
     * @return Category
     */
    protected function findCategory(Wallet $wallet, $title)
    {
        $foundCategories = $this->em->getRepository('AppBundle:Category')
            ->findSimilarByTitle($wallet, $title);

        if (is_null($foundCategories)) {
            $category = new Category;
            $category->setTitle($title);
            $category->setWallet($wallet);
            $this->em->persist($category);
        } else {
            $category = $foundCategories[0];
        }
        return $category;
    }

    protected function getRegularExpression()
    {
        $words = implode('|', $this->words);
        $regex = str_replace(
            [':words', ':money', ':category'],
            [$words, $this->moneyParser->getPattern(), $this->categoryRegex],
            $this->regularExpression
        );
        return $regex;
    }
}
