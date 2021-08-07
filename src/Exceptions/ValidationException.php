<?php

namespace Wmandai\Mpesa\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public $errors = [];

    public function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('The given data failed to pass validation. ' . print_r($this->errors, true));
    }
}
