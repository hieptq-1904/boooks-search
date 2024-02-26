<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class BaseException extends Exception
{
    public $httpCode;

    public function __construct(
        $message = '',
        $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        Exception $previous = null
    ) {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response()->json([
            'code' => $this->code,
            'message' => $this->message,
        ], $this->httpCode);
    }
}
