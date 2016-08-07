<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Currency;

/**
 * Class Wallet
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="wallets", uniqueConstraints={@ORM\UniqueConstraint(name="account", columns={"account"})})
 * @ORM\Entity
 */
class Wallet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="account", type="string", length=64)
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="default_currency", type="string", length=32)
     */
    private $defaultCurrency;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param int $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return Currency
     */
    public function getDefaultCurrency()
    {
        return new Currency($this->defaultCurrency);
    }

    /**
     * @param Currency $currency
     * @return Wallet
     */
    public function setDefaultCurrency(Currency $currency)
    {
        $this->defaultCurrency = $currency->getName();
        return $this;
    }
}
