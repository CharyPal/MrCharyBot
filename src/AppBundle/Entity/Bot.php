<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TelegramBot\Api\BotApi;


/**
 * MessageOffset
 *
 * @ORM\Table(name="bots", uniqueConstraints={@ORM\UniqueConstraint(name="bot_name", columns={"title"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BotRepository")
 */
class Bot
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=32, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=128, nullable=false)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="botan_token", type="string", length=128, nullable=true)
     */
    private $botanToken;

    /**
     * @var int
     *
     * @ORM\Column(name="last_offset", type="integer")
     */
    private $lastOffset = 0;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetimetz")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=64, nullable=true)
     */
    private $secret;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Bot
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set lastOffset
     *
     * @param integer $lastOffset
     *
     * @return Bot
     */
    public function setLastOffset($lastOffset)
    {
        $this->lastOffset = $lastOffset;

        return $this;
    }

    /**
     * Get lastOffset
     *
     * @return int
     */
    public function getLastOffset()
    {
        return $this->lastOffset;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @return Bot
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Bot
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getBotanToken()
    {
        return $this->botanToken;
    }

    /**
     * @param string $botanToken
     * @return $this
     */
    public function setBotanToken($botanToken)
    {
        $this->botanToken = $botanToken;
        return $this;
    }

    /** @var BotApi */
    private $api;

    /**
     * @return BotApi
     */
    public function getApi()
    {
        if (is_null($this->api))
            $this->api = new BotApi($this->getToken(), $this->getBotanToken());
        return $this->api;
    }
}
