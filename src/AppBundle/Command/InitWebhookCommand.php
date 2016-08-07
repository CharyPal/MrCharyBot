<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\Exception;

class InitWebhookCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:bot:webhook")
            ->setDescription("Initialize webhook for the bot")
            ->addArgument("bot", InputArgument::REQUIRED, "Bot title")
            ->addOption('disable', 'd', InputOption::VALUE_NONE, 'Disable webhook instead of enabling');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $botTitle = $input->getArgument("bot");
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $bot = $em->getRepository('AppBundle:Bot')->findOneBy(['title' => $botTitle]);
        if (is_null($bot)) {
            $io->error("Bot {$botTitle} not found");
            return;
        }
        
        if ($bot->getSecret() && !$input->getOption('disable')) {
            $io->error("Bot {$bot->getTitle()} already has a secret set up");
            return;
        }

        try {

            if ($input->getOption('disable')) {
                $url = '';
                $success = [
                    "Bot {$bot->getTitle()} has successfully disabled webhook",
                    "Please use getUpdates() API method again"
                ];
                $bot->setSecret(null);
            } else {
                $secret = $io->askHidden("Please provide the secret for bot {$bot->getTitle()}");
                $bot->setSecret($secret);
                $router = $this->getContainer()->get('router');
                $host = $this->getContainer()->getParameter('app_domain');
                $router->getContext()->setHost($host);
                $url = $router->generate('app_telegram_handle', ['secret' => $bot->getSecret()], $router::ABSOLUTE_URL);
                $success = [
                    "Bot {$bot->getTitle()} has successfully initialized the webhook",
                    "Webhook url: {$url}",
                    "Using getUpdates() API call is not available now on"
                ];
            }


            $api = $this->getContainer()->get('shaygan.telegram_bot_api');
            $api->setWebhook($url);
            $em->persist($bot);
            $em->flush();

            $io->success($success);

        } catch (Exception $e) {
            $this->getContainer()->get('logger')->error($e->getMessage(), ['error' => $e]);
            throw $e;
        }
    }
}
