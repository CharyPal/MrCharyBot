<?php

namespace Tests\AppBundle\Handler;

use AppBundle\Entity\Wallet;
use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Income;
use AppBundle\MoneyHelper;
use Money\Currency;

/**
 * Class IncomeHandlerTest
 * @package Tests\AppBundle\Handler
 * @group IncomeHandler
 */
class IncomeTestCase extends AbstractHandlerTestCase
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

        $handler = new Income($this->getTemplating(), $this->getLogger(), $this->getEmMock($repositoryMock));
        $handler->setMoneyParser(new MoneyHelper(['EUR', 'USD', 'UAH']));
        $handler->onMessageReceive($event);

        $this->assertTrue($event->isPropagationStopped(), 'Propagation error');
        $this->assertTrue($event->hasResponse(), 'Message does not have a response');

        $this->assertResponseMessage($event->getResponse(), 321, true);
        $this->assertEquals(123456, $event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @group Handlers
     * @group IncomeShouldHandle
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testShouldHandle(MessageReceive $event)
    {
        $handler = new Income($this->getTemplating(), $this->getLogger(), $this->getEmMock());
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
        $handler = new Income($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $handler->setMoneyParser(new MoneyHelper(['EUR', 'USD', 'UAH']));
        $this->assertFalse($this->callProtected($handler, 'shouldHandle', [$event]), 'Message identified incorrectly');
    }

    /**
     * @group Category
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     * @param $category
     */
    public function testGetCategory(MessageReceive $event, $category)
    {
        $handler = new Income($this->getTemplating(), $this->getLogger(), $this->getEmMock());
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
            [new MessageReceive($this->getMessageMock('/inc 35 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('/inc35 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('inc 45 USD ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('inc45 USD ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('inc 45 EUR ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('inc45 EUR ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ 45.12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+45.12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ 45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('+45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('inc 45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('inc45.12')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('inc 45.12 UAH')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('inc45.12 UAH')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('inc 45.12USD')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('inc45.12USD')), 'uncategorized'],
            [new MessageReceive($this->getMessageMock('+ 45.12 uah ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+45.12 uah ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ 45.12 UAH ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+45.12 UAH ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ 45,12 eur ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+45,12 eur ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ $45,12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+$45,12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ 45.12EUR ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+45.12EUR ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ €45.12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+€45.12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ ₴45.12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+₴45.12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ ₴45,12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+₴45,12 ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+ 45,12UAH ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('+45,12UAH ставкинаспорт')), 'ставкинаспорт'],
            [new MessageReceive($this->getMessageMock('inc 45 USD wage')), 'wage'],
            [new MessageReceive($this->getMessageMock('inc45 USD wage')), 'wage'],
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
