<?php

namespace Tests\AppBundle\Handler;

use AppBundle\Entity\Wallet;
use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Expense as ExpenseHandler;
use AppBundle\MoneyHelper;
use Money\Currency;

/**
 * Class ExpenseHandlerTest
 * @package AppBundle\Handler
 *
 * @group ExpenseHandler
 */
class ExpenseTestCase extends AbstractHandlerTestCase
{

    /**
     * @group Handlers
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $wallet = $this->getMockBuilder(Wallet::class)
            ->disableOriginalConstructor()
            ->getMock();
        $wallet->method('getDefaultCurrency')->will($this->returnValue(new Currency('UAH')));

        $repositoryMock = $this->getRepositoryMock($wallet);
        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($wallet));

        $handler = new ExpenseHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock($repositoryMock));
        $handler->setMoneyParser(new MoneyHelper(['EUR', 'USD', 'UAH']));
        $handler->onMessageReceive($event);

        $this->assertTrue($event->isPropagationStopped(), 'Propagation error');
        $this->assertTrue($event->hasResponse(), 'Message does not have a response');
        
        $this->assertResponseMessage($event->getResponse(), 321, true);
        $this->assertEquals(123456, $event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @group Handlers
     * @group ExpenseShouldHandle
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testShouldHandle(MessageReceive $event)
    {
        $handler = new ExpenseHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $handler->setMoneyParser(new MoneyHelper(['EUR', 'USD', 'UAH']));
        $this->assertTrue($this->callProtected($handler, 'shouldHandle', [$event]), 'Message not identified');
    }

    /**
     * @group Handlers
     * @dataProvider getInvalidEvents
     * @param MessageReceive $event
     */
    public function testShouldntHandle(MessageReceive $event)
    {
        $handler = new ExpenseHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $handler->setMoneyParser(new MoneyHelper(['EUR', 'USD', 'UAH']));
        $this->assertFalse($this->callProtected($handler, 'shouldHandle', [$event]), 'Message identified incorrectly');
    }

    /**
     * @group ExpenseCategory
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     * @param $category
     */
    public function testGetCategory(MessageReceive $event, $category)
    {
        $handler = new ExpenseHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $handler->setMoneyParser(new MoneyHelper(['EUR', 'USD', 'UAH']));
        $this->callProtected($handler, 'shouldHandle', [$event]);
        $this->assertEquals($category, $this->callProtected($handler, 'getCategoryName', [$event]));
    }

    /**
     * @return array
     */
    public function getValidEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('/exp 35 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('/exp35 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('exp 45 USD кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('exp45 USD кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('exp 45 EUR кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('exp45 EUR кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- 45.12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-45.12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- 45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('-45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('exp 45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('exp45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('exp 45.12 UAH')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('exp45.12 UAH')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('exp 45.12USD')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('exp45.12USD')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('- 45.12 uah кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-45.12 uah кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- 45.12 UAH кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-45.12 UAH кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- 45,12 eur кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-45,12 eur кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- $45,12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-$45,12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- 45.12EUR кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-45.12EUR кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- €45.12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-€45.12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- ₴45.12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-₴45.12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- ₴45,12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-₴45,12 кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('- 45,12UAH кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('-45,12UAH кава')), 'кава'],
            [new MessageReceive($this->getMessageMock('exp 45 USD coffee')), 'coffee'],
            [new MessageReceive($this->getMessageMock('exp45 USD coffee')), 'coffee'],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('трололо')), false],
            [new MessageReceive($this->getMessageMock('ololo тролого 5 горіхів на гівно')), false]
        ];
    }
}
