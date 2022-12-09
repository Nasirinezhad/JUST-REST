<?php

namespace Nasirinezhad\JustRest;

class RException extends \Exception 
{
    private $statusCode;
    private $errorCode;

    public function __construct(String $message, $statusCode = 400, $errorCode = null) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode == null ? $statusCode : $errorCode;
    }
    public function getErrorCode()
    {
        return $this->errorCode;
    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }

}