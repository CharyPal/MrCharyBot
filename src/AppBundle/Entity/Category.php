<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Money\Money;

/**
 * Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    use TimestampableEntity;
    use WalletBelongingEntity;

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var Money
     */
    private $expenseAmount;

    /**
     * @var Money
     */
    private $incomeAmount;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param Money $money
     * @return $this
     */
    public function setExpenseAmount(Money $money)
    {
        $this->expenseAmount = $money;
        return $this;
    }

    /**
     * @return Money
     */
    public function getExpenseAmount()
    {
        return $this->expenseAmount;
    }

    /**
     * @param Money $money
     * @return $this
     */
    public function setIncomeAmount(Money $money)
    {
        $this->incomeAmount = $money;
        return $this;
    }

    /**
     * @return Money
     */
    public function getIncomeAmount()
    {
        return $this->incomeAmount;
    }

    /**
     * @return Money
     */
    public function getBalance()
    {
        return $this->getIncomeAmount()->subtract($this->getExpenseAmount());
    }
}

