<?php


namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\DateTime;
use TelegramBot\Api\Types\Update;
use Tests\AppBundle\MockTrait;

/**
 * Class TelegramControllerTest
 * @package Tests\AppBundle\Controller
 * @group Controllers
 * @group TelegramController
 */
class TelegramControllerTest extends WebTestCase
{
    use MockTrait;
    /**
     * @dataProvider getUpdates
     * @param Update $update
     */
    public function testHandle(Update $update)
    {
        $client = static::createClient([], ['HTTPS' => true]);

        $crawler = $client->request('POST', '/message/123456', [], [], [], $update->toJson());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function getUpdates()
    {
        return [
            [Update::fromResponse(['update_id' => 123456, 'message' => [
                'message_id' => 123456, 'text' => '/start', 'chat' => $this->chatMockData,
                'from' => $this->userMockData, 'date' => 1234567890]])
            ],
        ];
    }

    /**
     * @group Update
     */
    public function testCreateUpdate()
    {
        $update = Update::fromResponse(['update_id' => 123456, 'message' => [
            'message_id' => 123456, 'text' => '/start', 'chat' => $this->chatMockData,
            'from' => $this->userMockData, 'date' => 1234567890]]);

        $this->assertNotNull($update->getMessage());

    }
}
