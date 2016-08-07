<?php

namespace AppBundle;

class AbstractResponse implements ResponseInterface
{
    protected $for;

    protected $replyTo;

    public function setFor($id)
    {
        $this->for = $id;
    }

    public function setReplyTo($id)
    {
        $this->replyTo = $id;
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }

    public function getFor()
    {
        return $this->for;
    }
}
