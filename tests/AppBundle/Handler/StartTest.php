<?php

namespace Tests\AppBundle\Handler;

use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Start;
use Doctrine\ORM\EntityRepository;

class StartTestCase extends AbstractHandlerTestCase
{
    /**
     * @group StartHandle
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->method('findOneBy')->willReturn(null);
        $em = $this->getEmMock($repository);
        $em->expects($this->once())
            ->method('persist')->willReturn($em);
        $em->expects($this->once())
            ->method('flush')->willReturn($em);

        
        $handler = new Start($this->getTemplating(), $this->getLogger(), $em);
        $handler->onMessageReceive($event);
        
        $this->assertTrue($event->isPropagationStopped(), 'Propagation error');
        $this->assertTrue($event->hasResponse(), 'Does not have any response');

        $this->assertResponseMessage($event->getResponse());
        $this->assertNull($event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testShouldHandle(MessageReceive $event)
    {
        $handler = new Start($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertTrue($this->callProtected($handler, 'shouldHandle', [$event]), 'Message identification error');
    }

    /**
     * @dataProvider getInvalidEvents
     * @param MessageReceive $event
     */
    public function testShouldntHandle(MessageReceive $event)
    {
        $handler = new Start($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertFalse($this->callProtected($handler, 'shouldHandle', [$event]), 'Message identification error');
    }

    public function getValidEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('/start'))],
            [new MessageReceive($this->getMessageMock('start'))],
            [new MessageReceive($this->getMessageMock('привіт'))],
            [new MessageReceive($this->getMessageMock('/привіт'))],
            [new MessageReceive($this->getMessageMock('hello'))],
            [new MessageReceive($this->getMessageMock('/hello'))],

        ];
    }

    public function getInvalidEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('ololo'))],
            [new MessageReceive($this->getMessageMock('трололо'))]
        ];
    }
}
