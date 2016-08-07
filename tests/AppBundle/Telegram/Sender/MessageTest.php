<?php

namespace Tests\AppBundle\Telegram\Sender;

use AppBundle\Entity\Bot;
use AppBundle\Telegram\Event\MessageRespond;
use AppBundle\Response\Message;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Message as TelegramMessage;
use Tests\AppBundle\AppTestCase;

class MessageTest extends AppTestCase
{
    /**
     * @dataProvider getEvents
     * @param MessageRespond $event
     */
    public function testOnMessageRespond(MessageRespond $event)
    {
        $handler = new \AppBundle\Telegram\Sender\Message;
        $handler->onMessageRespond($event);

        $this->assertTrue($event->isPropagationStopped());
    }

    public function getEvents()
    {
        $api = $this->getMockBuilder(BotApi::class)
            ->disableOriginalConstructor()
            ->getMock();
        $api->expects($this->atLeastOnce())
            ->method('sendMessage')
            ->willReturn(new TelegramMessage);

        $bot = $this->getMockBuilder(Bot::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bot->method('getApi')->willReturn($api);

        return [
            [new MessageRespond($this->getMessageMock('hello'), new Message('Ololo Trololo'), $bot)],
            [new MessageRespond($this->getMessageMock('bye bye'), new Message('Ololo Trololo'), $bot)],
        ];
    }
}
