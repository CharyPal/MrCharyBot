<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Bot;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class BotsFixture
 * @package AppBundle\DataFixtures\ORM
 */
class BotsFixture extends AbstractFixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $bot = new Bot;
        $bot->setTitle('Test bot')
            ->setToken('asdkjaskdjaskda:asdasdasdasdasds')
            ->setSecret(123456)
            ->setUpdatedAt(new \DateTime());

        $manager->persist($bot);
        $manager->flush();
    }
}
