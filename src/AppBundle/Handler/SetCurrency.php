<?php

namespace AppBundle\Handler;

use AppBundle\Event;
use AppBundle\Response\Message;
use Money\Currency;

/**
 * Class CurrencyHandler
 * @package AppBundle\Handler
 *
 * Handle wallet default currency
 */
class SetCurrency extends AbstractHandler
{
    protected $words = ['setcurrency', 'currency'];

    protected $regularExpression = '/^[\/]?(:words)\s(?P<cName>[a-z]{3})$/i';

    public function onMessageReceive(Event $event)
    {
        if (!$this->shouldHandle($event))
            return;

        $event->stopPropagation();
        $wallet = $this->getWallet($event);
        $oldCurrency = $wallet->getDefaultCurrency();
        $currency = $this->getCurrency();
        if ($oldCurrency->equals($currency)) {
            $event->setResponse(new Message(
                $this->render(':message/currency:samecurrency.md.twig', ['wallet' => $wallet])
            ));
        } else {
            $wallet->setDefaultCurrency($currency);
            $this->em->persist($wallet);
            $this->em->flush();
            
            $event->setResponse(new Message($this->render(
                ':message:currency.md.twig',
                ['wallet' => $wallet, 'oldCurrency' => $oldCurrency])
            ));
        }
    }

    /**
     * @return SetCurrency
     */
    private function getCurrency()
    {
        return new Currency(strtoupper($this->matches['cName']));
    }
}
