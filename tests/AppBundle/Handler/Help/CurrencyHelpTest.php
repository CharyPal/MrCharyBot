<?php

namespace Tests\AppBundle\Handler\Help;


use AppBundle\Telegram\Event\MessageReceive;
use AppBundle\Handler\Help\CurrencyHelp as CurrencyHandler;
use Tests\AppBundle\Handler\AbstractHandlerTestCase;

/**
 * Class CurrencyHandlerTest
 * @package Tests\AppBundle\Handler\Help
 *
 * @group HelpCurrency
 */
class CurrencyHelpTest extends AbstractHandlerTestCase
{
    /**
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testOnMessageReceive(MessageReceive $event)
    {
        $handler = new CurrencyHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $handler->setCurrencies(['USD' => 'United States Dollar', 'EUR' => 'Euro', 'UAH' => 'Ukrainian Hryvnia']);

        $handler->onMessageReceive($event);
        $this->assertTrue($event->isPropagationStopped(), 'Message identification error');
        $this->assertTrue($event->hasResponse(), 'No response');
        $this->assertResponseMessage($event->getResponse());
        $this->assertNull($event->getResponse()->getReplyTo(), 'Reply error');
    }

    /**
     * @dataProvider getValidEvents
     * @param MessageReceive $event
     */
    public function testShouldHandle(MessageReceive $event)
    {
        $handler = new CurrencyHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertTrue($this->callProtected($handler, 'shouldHandle', [$event]), 'Message identification error');
    }

    /**
     * @dataProvider getInvalidEvents
     * @param MessageReceive $event
     */
    public function testShouldNotHandle(MessageReceive $event)
    {
        $handler = new CurrencyHandler($this->getTemplating(), $this->getLogger(), $this->getEmMock());
        $this->assertFalse($this->callProtected($handler, 'shouldHandle', [$event]), 'False message identification');
    }

    public function getValidEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('/help currency'))],
            [new MessageReceive($this->getMessageMock('/help currencies'))],
            [new MessageReceive($this->getMessageMock('help currency'))],
            [new MessageReceive($this->getMessageMock('інфо валюта'))],
            [new MessageReceive($this->getMessageMock('/інфо валюти'))],
        ];
    }

    public function getInvalidEvents()
    {
        return [
            [new MessageReceive($this->getMessageMock('/help'))],
            [new MessageReceive($this->getMessageMock('currencies'))],
            [new MessageReceive($this->getMessageMock('help'))],
            [new MessageReceive($this->getMessageMock('інфо'))],
            [new MessageReceive($this->getMessageMock('інфо'))],
        ];
    }

}
