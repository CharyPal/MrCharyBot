<?php

namespace Tests\AppBundle\Handler;

use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Show;

class ShowTestCase extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $handle = new Show($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $handle->onMessageReceive($event);

        $this->assertTrue($event->isPropagationStopped(), 'Propagation error');
        $this->assertTrue($event->hasResponse(), 'Does not have any response');
        
        $this->assertResponseMessage($event->getResponse());
        $this->assertNull($event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testShouldHanle(MessageReceive $event)
    {
        $handle = new Show($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->callProtected($handle, 'shouldHandle', [$event]);
    }

    /**
     * @return array
     */
    public function getValidEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('show'))],
            [new MessageReceive($this->getMessageMock('/show'))],
        ];
    }
}
