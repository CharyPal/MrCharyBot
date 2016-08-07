<?php

namespace Test\AppBundle\Handler\Show;

use AppBundle\Entity\Category;
use AppBundle\Entity\Wallet;
use AppBundle\Telegram\Event\MessageReceive;
use Money\Currency;
use AppBundle\Handler\Show\Category as CategoryHandler;
use Tests\AppBundle\Handler\AbstractHandlerTestCase;

class CategoryTestCase extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $wallet = $this->getMockBuilder(Wallet::class)
            ->getMock();
        $wallet->method('getDefaultCurrency')->will($this->returnValue(new Currency('UAH')));

        $one = new Category;
        $one->setTitle('Food');

        $two = new Category;
        $two->setTitle('Alcohol');

        $repositoryMock = $this->getRepositoryMock($wallet);
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($wallet));
        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->willReturn([$one, $two]);

        $handler = new CategoryHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock($repositoryMock));
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
        $handle = new CategoryHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->callProtected($handle, 'shouldHandle', [$event]);
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('show category'))],
            [new MessageReceive($this->getMessageMock('show cats'))],
            [new MessageReceive($this->getMessageMock('show categories'))],
        ];
    }
}
