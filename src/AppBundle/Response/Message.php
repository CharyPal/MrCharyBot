<?php

namespace AppBundle\Response;

use AppBundle\AbstractResponse;

class Message extends AbstractResponse
{
    const PARSE_MODE_MARKDOWN = 'Markdown';
    const PARSE_MODE_HTML = 'HTML';

    /** @var  string */
    private $content;

    /** @var string  */
    private $parseMode = self::PARSE_MODE_MARKDOWN;

    /** @var bool  */
    private $disableNotification = false;

    public function __construct($content, $replyTo = null)
    {
        $this->setContent($content);
        if (!is_null($replyTo))
            $this->setReplyTo($replyTo);
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getParseMode()
    {
        return $this->parseMode;
    }

    /**
     * @param string $parseMode
     */
    public function setParseMode($parseMode)
    {
        $this->parseMode = $parseMode;
    }

    /**
     * @return boolean
     */
    public function isDisableNotification()
    {
        return $this->disableNotification;
    }

    /**
     * @param boolean $disableNotification
     */
    public function setDisableNotification($disableNotification)
    {
        $this->disableNotification = $disableNotification;
    }
}
