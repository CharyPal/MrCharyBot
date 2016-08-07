<?php

namespace Tests\AppBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\User;

trait MockTrait
{

    protected $userMockData = ['id' => 123, 'first_name' => 'Mr.Tester'];
    protected $chatMockData = ['id' => 321, 'type' => 'private'];

    /**
     * @param null $object
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRepositoryMock($object = null)
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        if (!is_null($object))
            $repositoryMock->method('find')->willReturn($object);

        return $repositoryMock;
    }

    /**
     * @param EntityRepository|null $repositoryMock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEmMock(EntityRepository $repositoryMock = null)
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        if (!is_null($repositoryMock))
            $emMock->method('getRepository')->willReturn($repositoryMock);

        return $emMock;
    }

    /**
     * @param $text
     * @param null $id
     * @param null $from
     * @param null $chat
     * @return Message
     */
    protected function getMessageMock($text, $id = null, $from = null, $chat = null)
    {

        if (is_null($id))
            $id = 123456;
        if (is_null($from))
            $from = $this->userMockData;
        if (is_null($chat))
            $chat = $this->chatMockData;

        return Message::fromResponse([
            'message_id' => $id, 'text' => $text, 'chat' => $chat, 'from' => $from, 'date' => 1234567890
        ]);
    }

    protected function getUserMock()
    {
        return User::fromResponse($this->userMockData);
    }

    protected function getChatMock()
    {
        return Chat::fromResponse($this->chatMockData);
    }
}
