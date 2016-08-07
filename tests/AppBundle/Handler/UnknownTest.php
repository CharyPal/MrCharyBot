<?php

namespace Tests\AppBundle\Handler;

use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Unknown;

class UnknownTestCase extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $handler = new Unknown($this->getTemplating(), $this->getLogger(), $this->getEmMock());

        $handler->onMessageReceive($event);

        $this->assertResponseMessage($event->getResponse());
        $this->assertEquals(123, $event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     */
    public function testShouldHandle(MessageReceive $event)
    {
        $handler = new Unknown($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertTrue(
            $this->callProtected($handler, 'shouldHandle', [$event])
        );
    }

    public function getEvents()
    {
        $message = $this->getMessageMock('Hello how are you', 123);

        $messageEvent = new MessageReceive($message);
        return [[$messageEvent]];
    }
}
