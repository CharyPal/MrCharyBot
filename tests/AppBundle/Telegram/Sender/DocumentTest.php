<?php


namespace Tests\AppBundle\Telegram\Sender;

use AppBundle\Entity\Bot;
use AppBundle\Response\Document;
use AppBundle\Telegram\Event\MessageRespond;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Message as TelegramMessage;
use Tests\AppBundle\AppTestCase;

class DocumentTest extends AppTestCase
{
    /**
     * @dataProvider getEvents
     * @param MessageRespond $event
     */
    public function testOnMessageRespond(MessageRespond $event)
    {
        $handler = new \AppBundle\Telegram\Sender\Document;
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
            [new MessageRespond(
                $this->getMessageMock('hello'),
                new Document(__DIR__.DIRECTORY_SEPARATOR.'DocumentTestFile.txt', 'testfile.txt'),
                $bot
            )],
            [new MessageRespond(
                $this->getMessageMock('bye bye'),
                new Document(__DIR__.DIRECTORY_SEPARATOR.'DocumentTestFile.txt', 'testfile.txt'),
                $bot
            )],
        ];
    }
}
