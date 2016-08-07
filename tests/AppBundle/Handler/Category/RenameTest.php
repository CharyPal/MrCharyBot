<?php

namespace Tests\AppBundle\Handler\Category;

use AppBundle\Entity\Category;
use AppBundle\Entity\Wallet;
use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Category\Rename;
use Tests\AppBundle\Handler\AbstractHandlerTestCase;

/**
 * Class RenameHandlerTest
 * @package Tests\AppBundle\Handler\Category
 */
class RenameTest extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $category = new Category;
        $wallet = new Wallet;
        $repository = $this->getRepositoryMock($category);
        $repository->method('findOneBy')
            ->willReturn($wallet, $category, null);

        $handler = new Rename($this->getTemplating(), $this->getLogger(), $this->getEmMock($repository));
        $handler->onMessageReceive($event);
        $this->assertTrue($event->isPropagationStopped(), 'Propagation error');
        $this->assertTrue($event->hasResponse(), 'Does not have any response');

        $this->assertResponseMessage($event->getResponse());
        $this->assertNull($event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     */
    public function testShouldHandle(MessageReceive $event)
    {
        $handler = new Rename($this->getTemplating(), $this->getLogger(), $this->getEmMock());

        $this->assertTrue($this->callProtected($handler, 'shouldHandle', [$event]));
        $this->assertEquals('ololo', $this->callProtected($handler, 'getOldTitle'));
        $this->assertEquals('trololo', $this->callProtected($handler, 'getNewTitle'));
    }

    public function getEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('setcategoryname ololo trololo'))],
            [new MessageReceive($this->getMessageMock('/setcategoryname ololo trololo'))],
        ];
    }
}
