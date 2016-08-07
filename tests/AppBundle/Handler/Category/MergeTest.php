<?php

namespace Tests\AppBundle\Handler\Category;

use AppBundle\Entity\Expense;
use AppBundle\Entity\Category;
use AppBundle\Entity\Wallet;
use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Category\Merge;
use Tests\AppBundle\Handler\AbstractHandlerTestCase;

/**
 * Class MergeHandlerTest
 * @package Tests\AppBundle\Handler\Category
 *
 * @group MergeHandler
 */
class MergeTest extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $category1 = new Category;
        $category2 = new Category;
        $wallet = new Wallet;
        $repository = $this->getRepositoryMock();
        $repository->method('findOneBy')
            ->willReturn($wallet, $category1, $category2);
        $repository->method('findBy')
            ->willReturn([new Expense(), new Expense(), new Expense()]);

        $handler = new Merge($this->getTemplating(), $this->getLogger(), $this->getEmMock($repository));
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
        $handler = new Merge($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertTrue($this->callProtected($handler, 'shouldHandle', [$event]), 'Message identification error');
        $this->assertNotNull($this->callProtected($handler, 'getOldTitle'), 'Message parsing error');
        $this->assertNotNull($this->callProtected($handler, 'getNewTitle'), 'Message parsing error');
    }

    public function getEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('mergecategory ololo trololo'))],
            [new MessageReceive($this->getMessageMock('/mergecategory ololo trololo'))],
            [new MessageReceive($this->getMessageMock('/mergecategory кафа кава'))],
            [new MessageReceive($this->getMessageMock('mergecategory кава coffee'))],
            [new MessageReceive($this->getMessageMock('/mergecategory coffee кава'))],
        ];
    }
}
