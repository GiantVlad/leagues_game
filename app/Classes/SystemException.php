<?php
namespace Classes;

use Exception;

/**
 * Class SystemException
 * @package Classes
 */
class SystemException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
