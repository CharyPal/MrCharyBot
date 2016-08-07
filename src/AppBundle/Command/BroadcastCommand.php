<?php

namespace AppBundle\Command;

use AppBundle\Response\Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BroadcastCommand
 * @package AppBundle\Command
 *
 * Broadcast the message from file to all the accounts
 */
class BroadcastCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:bot:broadcast")
            ->setDescription("Send broadcast message to all accounts subscribed")
            ->addArgument("bot", InputArgument::REQUIRED, "Bot that will send message")
            ->addArgument("file", InputArgument::REQUIRED, "File with message");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');
        $fs = $this->getContainer()->get('filesystem');
        if (!$fs->exists($filePath)) {
            $io->error(["File {$filePath} not found.", "Cannot proceed"]);
            return;
        }

        if (!is_readable($filePath)) {
            $io->error(["File {$filePath} is not readable", "Cannot proceed"]);
            return;
        }

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $bot = $em->getRepository('AppBundle:Bot')->findOneBy(['title' => $input->getArgument('bot')]);
        if (!$bot) {
            $io->error(["Bot {$input->getArgument('bot')} was not found.", "Cannot proceed"]);
            return;
        }


        $io->title("Broadcast: using {$bot->getTitle()}");
        $io->comment("The following message will be broadcasted:");
        $io->newLine(2);
        $contents = file_get_contents($filePath, null, null, null, 4096); // 4096 max message length
        $io->writeln($contents);
        $io->newLine(2);

        $answer = $io->ask("Do you confirm sending this broadcast? (y/N)", "n");

        if (!in_array($answer, ['yes', 'y'])) {
            $io->warning(["Broadcast has not been sent.", "Exiting"]);
            return;
        }

        $io->comment("Initializing the broadcast...");

        $sent = 0;
        $wallets = $em->getRepository('AppBundle:Wallet')->findAll();
        $notSent = 0;
        foreach ($wallets as $wallet) {
            try {
                $bot->getApi()->sendMessage($wallet->getAccount(), $contents, Message::PARSE_MODE_MARKDOWN, true);
                $sent++;
                if ($sent % 10== 0) {
                    $io->comment("Sent {$sent} messages.");
                }
            } catch (\Exception $e) {
                $notSent++;
                $io->warning(["Sending message to {$wallet->getAccount()} failed.", $e->getMessage()]);
            }
        }

        if ($sent > 0) {
            $io->success("Broadcast sent to {$sent} recipients");
        } else {
            $io->caution("Broadcast didn't find even one recipient");
        }

        if ($notSent > 0) {
            $io->caution("Broadcast failed to be sent to {$notSent} recipients");
        }
    }
}
