<?php

namespace Tests\AppBundle\Telegram\Response;

use AppBundle\Response\Message;
use Tests\AppBundle\AppTestCase;

class MessageTest extends AppTestCase
{
    public function testMessage()
    {
        $message = new Message('ololo', null);
        
        $this->assertNull($message->getReplyTo());
        $this->assertEquals($message::PARSE_MODE_MARKDOWN, $message->getParseMode());
    }
}
