<?php

namespace Classes;

/**
 * Class Request
 * @package Classes
 */
class Request
{
    /**
     * @var array|string $storage
     */
    private $storage;

    public function __construct() {
        $this->storage = $this->cleanInput($_REQUEST);
    }

    /**
     * @param $name
     * @return bool|mixed|string
     */
    public function __get($name) {
        if (isset($this->storage[$name])) {
            return $this->storage[$name];
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @return array|string
     */
    private function cleanInput($data) {
        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $key => $value) {
                $cleaned[$key] = $this->cleanInput($value);
            }
            return $cleaned;
        }
        return trim(htmlspecialchars(strip_tags($data), ENT_QUOTES));
    }

    /**
     * @return array|string
     */
    public function getRequestEntries()
    {
        return $this->storage;
    }
}
