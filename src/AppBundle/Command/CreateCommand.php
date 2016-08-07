<?php

namespace AppBundle\Command;

use AppBundle\Entity\Bot;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:bot:create")
            ->setDescription("Create Telegram bot")
            ->addArgument("title", InputArgument::REQUIRED, "Bot title");

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument("title");
        $io = new SymfonyStyle($input, $output);
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $bot = $em->getRepository('AppBundle:Bot')->findOneBy(['title' => $title]);
        if (!is_null($bot)) {
            $io->error([
                "Cannot create bot {$bot->getTitle()}.",
                "Bot {$bot->getTitle()} already exists."
            ]);
            return;
        }

        $bot = new Bot;
        $bot->setTitle($title);
        $bot->setUpdatedAt(new \DateTime);
        $em->persist($bot);
        $em->flush();

        $io->success("Bot {$bot->getTitle()} has been created.");
    }
}
