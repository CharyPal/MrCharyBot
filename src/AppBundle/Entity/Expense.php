<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Money\Currency;
use Money\Money;

/**
 * Class Expense
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="expenses")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExpenseRepository")
 */
class Expense implements ReportingEntity
{
    use TimestampableEntity;
    use AmountableEntity;
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
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", fetch="EAGER")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Expense
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        return $this;
    }
}
