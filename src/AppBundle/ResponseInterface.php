<?php

namespace AppBundle;

/**
 * Interface ResponseInterface
 * @package AppBundle
 *
 * This interface describes the generic response interface
 */
interface ResponseInterface
{
    /**
     * Who is this response for
     * @return string
     */
    public function getFor();

    /**
     * If value provided this response is a direct response to message.
     * Otherwise just a conversation response.
     * @return string|null
     */
    public function getReplyTo();
}
