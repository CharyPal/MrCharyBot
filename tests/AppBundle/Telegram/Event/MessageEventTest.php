<?php

namespace Tests\AppBundle\Telegram\Event;

use AppBundle\Telegram\Event\MessageReceive;
use Tests\AppBundle\Handler\AbstractHandlerTestCase;

/**
 * Class MessageEventTest
 * @package Tests\AppBundle\Telegram\Event
 * @group MessageEvent
 */
class MessageEventTest extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getIncomingMessageEvents
     * @param MessageReceive $messageEvent
     */
    public function testMessageAttributes(MessageReceive $messageEvent)
    {
        $this->assertEquals(123456, $messageEvent->getMessageId());
        $this->assertNotNull($messageEvent->getMessageText());
        $this->assertEquals(321, $messageEvent->getMessageChatId(), 'Message chat id is not set');
        $dateTime = new \DateTime;
        $dateTime->setTimestamp(1234567890);
        $this->assertEquals($dateTime, $messageEvent->getMessageDate());
        $this->assertEquals('Mr.Tester', $messageEvent->getMessageAuthor()->getFirstName());
        $this->assertFalse($messageEvent->hasResponse());
    }

    public function getIncomingMessageEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('/start', 123456))],
            [new MessageReceive($this->getMessageMock('привіт бот', 123456))],
            [new MessageReceive($this->getMessageMock('як діла?', 123456))],
            [new MessageReceive($this->getMessageMock('/welcome', 123456))],
            [new MessageReceive($this->getMessageMock('/how are you', 123456))],
            [new MessageReceive($this->getMessageMock('/hello', 123456))],
        ];
    }
}
