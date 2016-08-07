<?php

namespace Tests\AppBundle\Handler;

use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Help;

class HelpTestCase extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     * @param bool $result
     */
    public function testOnMessageReceive(MessageReceive $event, $result)
    {
        if (!$result)
            return;

        $handler = new Help($this->getTemplating(), $this->getLogger(), $this->getEmMock());

        $handler->onMessageReceive($event);
        $this->assertTrue($event->isPropagationStopped(), 'Propagation error');
        $this->assertTrue($event->hasResponse(), 'Does not have any response');

        $this->assertResponseMessage($event->getResponse());
        $this->assertNull($event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     * @param $result
     */
    public function testShouldHandle(MessageReceive $event, $result)
    {
        $handler = new Help($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertEquals(
            $result,
            $this->callProtected($handler, 'shouldHandle', [$event]),
            "Message identification error"
        );
    }

    public function getEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('ololo')), false],
            [new MessageReceive($this->getMessageMock('/help')), true],
            [new MessageReceive($this->getMessageMock('help')), true],
            [new MessageReceive($this->getMessageMock('/інфо')), true],
            [new MessageReceive($this->getMessageMock('інфо')), true],
            [new MessageReceive($this->getMessageMock('/допоможи')), true],
            [new MessageReceive($this->getMessageMock('допоможи')), true],
            [new MessageReceive($this->getMessageMock('трололо')), false],
            [new MessageReceive($this->getMessageMock('/help витрати')), false],
            [new MessageReceive($this->getMessageMock('help expense')), false],
        ];
    }
}
