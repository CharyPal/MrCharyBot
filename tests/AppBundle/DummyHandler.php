<?php

namespace Tests\AppBundle;

use TelegramBot\Api\Types\Update;

class DummyHandler extends \AppBundle\Telegram\UpdateHandler
{
    /**
     * @param Update $update
     */
    public function handle(Update $update)
    {
        // do nothing instead of handling the api
        return;
    }
}
