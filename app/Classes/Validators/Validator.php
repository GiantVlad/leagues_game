<?php

namespace Classes\Validators;

use Classes\DbConnection;
use Classes\Request;
use Classes\SystemException;
use Classes\User;


/**
 * Class Validator
 * @package Classes\Validators
 */
abstract class Validator
{
    protected $errors = [];
    protected $request;
    protected $db;

    public function __construct(Request $request, DbConnection $db)
    {
        $this->request = $request;
        $this->db = $db;
    }

    /**
     * @return array
     */
    abstract public function validate();

    /**
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    protected function checkStringLength(string $value, int $min, int $max) {
        return strlen($value) >= $min && strlen($value) <= $max;
    }

    /**
     * @param string $field
     * @return bool|mixed|void
     * @throws SystemException
     */
    protected function getValidUser(string $field)
    {
        $user = false;
        if (!$this->request->{$field}) {
            $this->errors[] = "{$field} is required.";
        } else {
            $userObj = new User($this->db);
            $user = $userObj->getUser((int) $this->request->{$field});
            if (!$user) {
                $this->errors[] = 'User with ID \''. $this->request->{$field} .'\' not found.';
            }

        }
        return $user;
    }
}
