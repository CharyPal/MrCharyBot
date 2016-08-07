<?php

namespace AppBundle;

use AppBundle\Entity\ReportingEntity;
use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class TotalsCalculator
{
    /** @var  Currency */
    private $currency;

    /** @var PairManagerInterface  */
    private $pairManager;

    /**
     * TotalsCalculator constructor.
     * @param PairManagerInterface $pairManager
     */
    public function __construct(PairManagerInterface $pairManager)
    {
        $this->pairManager = $pairManager;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Sum reporting entities by category.
     * Will return the amount of categories with totals
     * @param array $entities
     * @param string $what
     * @return array
     */
    public function sumByCategory(array $entities, $what)
    {
        $categories = [];
        $getter = 'get'.ucfirst($what).'Amount';
        $setter = 'set'.ucfirst($what).'Amount';
        foreach ($entities as $entity) {
            /** @var ReportingEntity $entity */
            if ($this->currency->equals($entity->getAmount()->getCurrency())) {
                $sameCurrencyAmount = $entity->getAmount();
            } else {
                $sameCurrencyAmount = $this->pairManager->convert($entity->getAmount(), $this->currency->getName());
            }

            $categoryId = $entity->getCategory()->getId();
            if (!array_key_exists($entity->getCategory()->getId(), $categories))
                $categories[$categoryId] = $entity->getCategory();

            $amount = call_user_func([$categories[$categoryId], $getter]);
            if (is_null($amount))
                $amount = $sameCurrencyAmount;
            else
                $amount = $amount->add($sameCurrencyAmount);
            call_user_func([$categories[$categoryId], $setter], $amount);
        }
        return $categories;
    }

    /**
     * @param array $entities
     * @return Money
     */
    public function sum(array $entities)
    {
        $totals = new Money(0, $this->currency);
        foreach ($entities as $entity) {
            if ($this->currency->equals($entity->getAmount()->getCurrency())) {
                $sameCurrencyAmount = $entity->getAmount();
            } else {
                $sameCurrencyAmount = $this->pairManager->convert($entity->getAmount(), $this->currency->getName());
            }
            $totals = $totals->add($sameCurrencyAmount);
        }
        return $totals;
    }

    /**
     * When income is less than expense then it's loss.
     * Otherwise it's profit.
     * 
     * @param Money $income
     * @param Money $expense
     * @return string
     */
    public function getNoun(Money $income, Money $expense)
    {
        return $income->lessThan($expense)
            ? 'Loss'
            : 'Profit';
    }
}
