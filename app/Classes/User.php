<?php

namespace Classes;

/**
 * Class User
 * @package Classes
 */
class User implements BaseModel
{
    /**
     * @var DbConnection $db
     */
    protected $db;

    /**
     * @var array ACTIONS
     */
    public const ACTIONS = [
        'set_role',
        'add_to_league',
        'remove_from_league',
    ];
    public function __construct(DbConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @return mixed|void
     * @throws SystemException
     */
    public function getUsers()
    {
        return $this->db->query('SELECT * FROM `users`');
    }

    /**
     * @param int $id
     * @return mixed|void
     * @throws SystemException
     */
    public function getUser(int $id)
    {
        return $this->db->query("
            SELECT `users`.*, `roles`.`key` as `role_key`, `leagues`.`name` as `league_name` FROM `users` 
                LEFT JOIN `roles` ON `roles`.`id` = `users`.`role_id`
                LEFT JOIN `leagues` ON `leagues`.`id` = `users`.`league_id` 
            WHERE `users`.`id` = {$id}
        ");
    }

    /**
     * @param int $user_id
     * @param string $league_name
     * @throws SystemException
     */
    public function addUserToLeague(int $user_id, string $league_name)
    {
        $role_key = ROLE::SOLDER;
        $this->db->query("
            UPDATE `users` 
            SET `users`.`league_id` = 
                (SELECT id FROM `leagues` WHERE `leagues`.`name` = '{$league_name}'),
                `users`.`role_id` =
                 (SELECT id FROM `roles` WHERE `roles`.`key` = '{$role_key}')
            WHERE `users`.`id` = {$user_id};
        ");
    }

    /**
     * @param int $user_id
     * @throws SystemException
     */
    public function removeUserFromLeague(int $user_id)
    {
        $this->db->query("
            UPDATE `users` 
            SET `users`.`league_id` = NULL,
                `users`.`role_id` = NULL
            WHERE `users`.`id` = {$user_id};
        ");
    }
}
