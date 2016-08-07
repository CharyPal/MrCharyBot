<?php

namespace AppBundle\Entity;

trait WalletBelongingEntity
{
    /**
     * @var Wallet
     *
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumn(name="wallet_id", referencedColumnName="id")
     */
    private $wallet;

    /**
     * @return Wallet
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * @param Wallet $wallet
     */
    public function setWallet(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }
}
