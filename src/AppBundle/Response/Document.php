<?php

namespace AppBundle\Response;

use AppBundle\AbstractResponse;

class Document extends AbstractResponse
{
    /** @var  string */
    private $path;

    /** @var  string */
    private $filename;

    public function __construct($path, $filename)
    {
        $this->path = $path;
        $this->filename = $filename;
    }

    public function getFile()
    {
        return $this->filename;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file mime type
     * @return string
     */
    public function getMimeType()
    {
        return mime_content_type($this->getPath());
    }
}
