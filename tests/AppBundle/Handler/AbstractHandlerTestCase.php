<?php

namespace Tests\AppBundle\Handler;

use AppBundle\ResponseInterface;
use AppBundle\Response\Message;
use Tests\AppBundle\AppTestCase;
use Psr\Log\NullLogger;

abstract class AbstractHandlerTestCase extends AppTestCase
{
    const MAX_MESSAGE_LENGTH = 4096;

    protected function getLogger()
    {
        return new NullLogger;
    }

    protected function getTemplating()
    {
        $templating = $this->getMockBuilder('\Symfony\Bundle\TwigBundle\TwigEngine')
            ->disableOriginalConstructor()
            ->getMock();
        $templating->method('render')->willReturn('Ololo');
        return $templating;
    }
    
    protected function assertResponseMessage(Message $response, $to = 321, $disableNotification = false)
    {
        $this->assertInstanceOf(Message::class, $response, 'Invalid response class');
        $this->assertEquals($disableNotification, $response->isDisableNotification(), 'Message notification error');
        $this->assertLessThanOrEqual($this::MAX_MESSAGE_LENGTH, strlen($response->getContent()), 'Message too long');
        $this->assertEquals(Message::PARSE_MODE_MARKDOWN, $response->getParseMode(), 'Incorrect parse mode');
        $this->assertEquals($to, $response->getFor(), 'Message recipient error');
    }
}
