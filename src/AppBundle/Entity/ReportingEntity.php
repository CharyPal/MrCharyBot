<?php

namespace AppBundle\Entity;

use Money\Money;

/**
 * Interface ReportingEntity
 * @package AppBundle\Entity
 *
 * Reporting entity interface
 */
interface ReportingEntity
{
    public function getCategory();

    public function setCategory(Category $category);

    public function getAmount();

    public function setAmount(Money $money);

}
