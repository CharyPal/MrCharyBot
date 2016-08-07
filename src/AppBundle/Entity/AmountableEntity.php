<?php

namespace AppBundle\Entity;

use Money\Currency;
use Money\Money;

/**
 * Class AmountableEntity
 * @package AppBundle\Entity
 */
trait AmountableEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=64)
     */
    private $currency;

    /**
     * @param Money $money
     * @return $this
     */
    public function setAmount(Money $money)
    {
        $this->amount = $money->getAmount();
        $this->currency = $money->getCurrency()->getName();
        return $this;
    }

    /**
     * @return Money|null
     */
    public function getAmount()
    {
        if (!$this->currency)
            return null;
        if (!$this->amount)
            return new Money(0, new Currency($this->currency));

        return new Money($this->amount, new Currency($this->currency));
    }
}
