<?php

namespace Classes;

/**
 * Class Role
 * @package Classes
 */
class Role implements BaseModel
{
    /**
     * @var string LEADER
     */
    const LEADER = 'leader';

    /**
     * @var string ALTERNATE
     */
    const ALTERNATE = 'alternate';

    /**
     * @var string SOLDER
     */
    const SOLDER = 'solder';

    /**
     * @var DbConnection $db
     */
    protected $db;
    public function __construct(DbConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $user_id
     * @param string $role_key
     * @throws SystemException
     */
    public function setRole(int $user_id, string $role_key)
    {
        $this->db->query("
            UPDATE `users` SET `users`.`role_id` = 
                (SELECT `id` FROM `roles` WHERE `key` = '{$role_key}' LIMIT 1)
            WHERE `users`.`id` = {$user_id};
        ");
    }
}
