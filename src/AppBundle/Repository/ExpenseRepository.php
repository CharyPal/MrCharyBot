<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Wallet;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use League\Period\Period;

class ExpenseRepository extends EntityRepository
{
    public function findByPeriod(Wallet $wallet, Period $period)
    {
        return $this->getEntityManager()
            ->createQuery('
                  SELECT e
                  FROM AppBundle:Expense e 
                  WHERE e.wallet = :wallet 
                    AND (e.createdAt BETWEEN :start AND :finish)
                  ORDER BY e.createdAt ASC
              ')->setParameter('wallet', $wallet)
                ->setParameter('start', $period->getStartDate(), Type::DATETIME)
                ->setParameter('finish', $period->getEndDate(), Type::DATETIME)
            ->getResult();
    }
}
