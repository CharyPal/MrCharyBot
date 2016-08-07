<?php

namespace AppBundle\Command;

use AppBundle\Entity\Bot;
use Monolog\Handler\FingersCrossedHandler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\Exception;
use Wrep\Daemonizable\Command\EndlessContainerAwareCommand;

class BotHandleCommand extends EndlessContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:bot:handle')
            ->setDescription("Handle messages from users")
            ->addArgument('bot', InputArgument::REQUIRED, 'Bot name to handle')
            ->setTimeout(1)
            ->setProcessTitle('Telegram message handler');
    }

    protected function starting(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Telegram message handler");
        parent::starting($input, $output);
    }

    protected function finalize(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Shutting down message handler. See you again soon");
        parent::finalize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        if ($io->isVerbose())
            $io->section("Bot {$input->getArgument('bot')} now working...");

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $bot = $em->getRepository('AppBundle:Bot')->findOneBy(['title' => $input->getArgument('bot')]);

        if (is_null($bot)) {
            $io->warning("Cannot handle. Bot {$input->getArgument('bot')} doesn't exist.");
            return;
        }

        try {
            $lastOffset = $bot->getLastOffset();
            if ($io->isVerbose())
                $io->writeln("Current offset: {$lastOffset}");
            if ($lastOffset === 0)
                $updates = $bot->getApi()->getUpdates();
            else
                $updates = $bot->getApi()->getUpdates($lastOffset+1);
            $updateCount = count($updates);
            if ($updateCount == 0)
                return; // do nothing if no updates found

            $updateHandler = $this->getContainer()->get('app.telegram.update_handler');
            $updateHandler->setBot($bot);
            if ($io->isVerbose())
                $io->writeln("Received {$updateCount} update(s).");

            foreach ($updates as $update) {
                $updateHandler->handle($update);
                $bot->setLastOffset($update->getUpdateId());
                $io->writeln("Processes update {$update->getUpdateId()}");
            }
            $bot->setUpdatedAt(new \DateTime);
            $em->persist($bot);
            $em->flush();

        } catch (Exception $e) {
            $io->error($e->getMessage());
            throw $e;
        }
        $this->flushMonolog();
    }

    protected function flushMonolog()
    {
        $logger = $this->getContainer()->get('logger');
        foreach ($logger->getHandlers() as $handler) {
            if (method_exists($handler, 'clear'))
                $handler->clear();
        }
    }
}
