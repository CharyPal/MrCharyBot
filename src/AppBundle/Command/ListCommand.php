<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:bot:list')
            ->setDescription("Show a list of currenctly installed bots");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $bots = $em->getRepository('AppBundle:Bot')->findBy([], ['title' => 'ASC']);

        if (count($bots) == 0) {
            $io->warning([
                "I cannot find a single bot for you master.",
                "Please create one with: app:bot:create"
            ]);
            return;
        }

        $rows = [];
        foreach ($bots as $bot)
            $rows[] = [
                $bot->getId(), $bot->getTitle(), $bot->getToken(),
                $bot->getUpdatedAt()->format('Y-m-d H:i:s'),
                $bot->getSecret() ? 'webhook': 'getUpdates'
            ];

        $io->table(['ID', 'Title', 'Token', 'Last Updated', 'Connection Type'], $rows);
    }


}
