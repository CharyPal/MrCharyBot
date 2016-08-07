<?php

namespace Tests\AppBundle\Handler;

use AppBundle\Entity\Wallet;
use AppBundle\Telegram\Event\MessageReceive;
use Money\Currency;
use AppBundle\Handler\SetCurrency as CurrencyHandler;

/**
 * Class CurrencyHandlerTest
 * @package Tests\AppBundle\Handler
 *
 * @group Handlers
 */
class SetCurrencyTestCase extends AbstractHandlerTestCase
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

        $repositoryMock = $this->getRepositoryMock($wallet);
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($wallet));

        $handler = new CurrencyHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock($repositoryMock));
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
        $handler = new CurrencyHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertTrue($this->callProtected($handler, 'shouldHandle', [$event]), 'Message Identification error');
        $this->assertInstanceOf(Currency::class, $this->callProtected($handler, 'getCurrency'), 'Currency not identified');
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('setcurrency UAH'))],
            [new MessageReceive($this->getMessageMock('/setcurrency USD'))],
            [new MessageReceive($this->getMessageMock('currency EUR'))],
            [new MessageReceive($this->getMessageMock('/currency PLN'))],
        ];
    }
}
