<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Expense as ExpenseEntity;


class Expense extends AbstractReportingHandler
{
    
    /** @var array */
    protected $words = ['-', 'exp'];

    protected function getReportingEntity()
    {
        return new ExpenseEntity;
    }

    protected function getReportingMessageTemplate()
    {
        return ':message:expense.md.twig';
    }
}
