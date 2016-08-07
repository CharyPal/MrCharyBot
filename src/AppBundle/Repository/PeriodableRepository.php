<?php

namespace AppBundle\Repository;

use Doctrine\DBAL\Types\Type;
use AppBundle\Entity\Wallet;
use Doctrine\ORM\Query\Expr\Join;
use League\Period\Period;

trait PeriodableRepository
{
    public function findByPeriod(Wallet $wallet, Period $period)
    {
        $qb = $this->createQueryBuilder('e');
        return $qb->where($qb->expr()->andX(
                $qb->expr()->eq('e.wallet', $wallet->getId()),
                $qb->expr()->between('e.createdAt', ':from', ':to')
            ))
            ->innerJoin('AppBundle:Category', 'ec', Join::ON, 'e.category_id = ec.id')
            ->addSelect('ec')
            ->orderBy('e.createdAt', 'ASC')
            ->groupBy('e.id')
            ->setParameter('from', $period->getStartDate(), Type::DATETIME)
            ->setParameter('to', $period->getEndDate(), Type::DATETIME)
            ->getQuery()
            ->getResult();
    }
}
