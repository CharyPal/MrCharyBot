<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Wallet;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function findSimilarByTitle(Wallet $wallet, $title)
    {
        $qb = $this->createQueryBuilder('ec');
        return $qb->select('ec', 'LEVENSHTEIN(ec.title, :title) as lvds')
            ->andWhere($qb->expr()->eq('ec.wallet', $wallet->getId()))
            ->andHaving($qb->expr()->lt('lvds', 3))
            ->orderBy('lvds', 'ASC')
            ->setParameter(':title', $title)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
