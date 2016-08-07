<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient([], ['HTTPS' => true]);

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Hello, I\'m @MrCharyBot!', $crawler->filter('.container h1')->text());
        $this->assertContains('Add @MrCharyBot on Telegram', $crawler->filter('#add-bot')->text());
    }
}
