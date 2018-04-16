<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response as IlluminateResponse;
use Log;

class ChallengeException extends Exception
{


    /**
     * Normal status response code
     *
     * @var integer
     */
    private $statusCode = IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR;


    private $messageToShow;


    private $errors;

    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @return void
     */
    public function __construct($messageToShow = 'Something were wrong.', $statusCode = IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR, $errors = [])
    {
        parent::__construct($messageToShow);
        
        $this->messageToShow = $messageToShow;
        $this->statusCode    = $statusCode;
        $this->errors        = $errors;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getMessageToShow()
    {
        return $this->messageToShow;
    }

    public function getErrors()
    {
        return $this->errors;
    }

  
}