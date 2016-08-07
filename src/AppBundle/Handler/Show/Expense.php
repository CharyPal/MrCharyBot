<?php

namespace AppBundle\Handler\Show;

use AppBundle\Event;
use AppBundle\Handler\AbstractHandler;
use AppBundle\Response\Message;
use AppBundle\TimeParser;
use AppBundle\TotalsCalculator;

/**
 * Class Expense
 * @package AppBundle\Handler\Show
 * @deprecated
 */
class Expense extends AbstractHandler
{
    protected $regularExpression = '/^[\/]?show\s(exp|expense|expenses)\s(?P<interval>.+)$/i';

    /** @var TotalsCalculator */
    private $calculator;

    public function setTotalsCalculator(TotalsCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @param Event $event
     * @deprecated
     */
    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        $wallet = $this->getWallet($event);
        $period = $this->getPeriod();

        $expenses = $this->em->getRepository('AppBundle:Expense')->findByPeriod($wallet, $period);
        $this->calculator->setCurrency($wallet->getDefaultCurrency());
        $categories = $this->calculator->sumByCategory($expenses, 'expense');
        $total = $this->calculator->sum($expenses);

        $event->stopPropagation();
        $message = new Message(
            $this->render(
                ':message/show:expense.md.twig',
                ['total' => $total, 'period' => $period, 'categories' => $categories])
        );
        $event->setResponse($message);
    }

    /**
     * @return \League\Period\Period
     */
    private function getPeriod()
    {
        return (new TimeParser())->parse($this->matches['interval']);
    }
}
