<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Income as IncomeEntity;

class Income extends AbstractReportingHandler
{
    /** @var array */
    protected $words = ['\+', 'inc'];

    protected function getReportingEntity()
    {
        return new IncomeEntity;
    }
    
    protected function getReportingMessageTemplate()
    {
        return ':message:income.md.twig';
    }
}
