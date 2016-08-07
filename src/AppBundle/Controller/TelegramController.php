<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;
use TelegramBot\Api\Types\Update;

/**
 * Class TelegramController
 * @package AppBundle\Controller
 *
 * Handles the webhook requests from Telegram.
 */
class TelegramController extends Controller
{
    /**
     * @Route("/message/{secret}", name="app_telegram_handle")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param $secret
     * @return JsonResponse
     * @throws HttpException
     */
    public function handleAction(Request $request, $secret)
    {
        $bot = $this->getDoctrine()
            ->getRepository('AppBundle:Bot')
            ->findOneBy(['secret' => $secret]);

        if (is_null($bot)) {
            $this->get('logger')->error('Secret does not match any bot', ['request' => $request, 'secret' => $secret]);
            throw $this->createNotFoundException();
        }

        try {
            $data0 = $request->getContent();
            $data = json_decode($data0, true);

            $update = Update::fromResponse($data);
            $telegramHandler = $this->get('app.telegram.update_handler');
            $telegramHandler->setBot($bot);
            $telegramHandler->handle($update);

            return new JsonResponse(['ok' => true]);
        } catch (\Exception $e) {
            $this->get('logger')->error('Error handling message', ['exception' => $e, 'data' => $data0]);
            return new JsonResponse(['ok' => true]);
        }
    }
}
