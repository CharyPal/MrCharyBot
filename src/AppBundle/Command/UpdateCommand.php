<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:bot:update')
            ->setDescription('Update bot attributes')
            ->addArgument('bot', InputArgument::REQUIRED, 'Bot to modify');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $em = $this->getContainer()
            ->get('doctrine.orm.entity_manager');
        $bot = $em->getRepository('AppBundle:Bot')
            ->findOneBy(['title' => $input->getArgument('bot')]);

        if (is_null($bot)) {
            $io->warning("Bot {$input->getArgument('bot')} not found.");
            return;
        }

        $questions = [
            ['question' => 'Bot title: ',               'default' => 'getTitle', 'method' => 'setTitle'],
            ['question' => 'Bot Telegram API token: ',  'default' => 'getToken', 'method' => 'setToken']
        ];

        foreach ($questions as $data) {
            $answer = $io->ask($data['question'], call_user_func([$bot, $data['default']]));
            call_user_func([$bot, $data['method']], $answer);
        }

        $em->persist($bot);
        $em->flush();
        $io->success("Bot {$bot->getTitle()} has been successfully updated");
    }
}
