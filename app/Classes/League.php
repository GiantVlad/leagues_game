<?php

namespace Classes;

/**
 * Class League
 * @package Classes
 */
class League implements BaseModel
{
    /**
     * @var DbConnection $db
     */
    protected $db;

    /**
     * @var string $table
     */
    private $table = 'leagues';

    /**
     * @var array ACTIONS
     */
    public const ACTIONS = [
        'create',
        'delete',
        'update'
    ];

    /**
     * League constructor.
     * @param DbConnection $db
     */
    public function __construct(DbConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $name
     * @return array|mixed|void|null
     * @throws SystemException
     */
    public function getLeagueByName(string $name)
    {
        return  $this->db->query("
            SELECT * FROM `{$this->table}` WHERE `name` = '{$name}';
        ");
    }

    /**
     * @return array|mixed|void|null
     * @throws SystemException
     */
    public function getLeaguesWithUsers()
    {
        return  $this->db->query("
            SELECT `{$this->table}`.*, `users`.`id` as `user_id`,
                `users`.`name` as `user_name`, `roles`.`name` as `role` 
            FROM `{$this->table}`
            JOIN `users` ON `{$this->table}`.`id` = `users`.`league_id`
            LEFT JOIN `roles` ON `users`.`role_id` = `roles`.`id`
            ORDER BY `{$this->table}`.`id`;
        ");
    }

    /**
     * @param string $name
     * @throws SystemException
     */
    public function deleteLeague(string $name)
    {
        $this->db->multi_query("
            START TRANSACTION;
            UPDATE `users` SET `role_id` = NULL, `league_id` = NULL 
                WHERE `league_id` = 
                    (SELECT id FROM `{$this->table}` WHERE `name` = '{$name}' LIMIT 1); 
            DELETE FROM `{$this->table}` WHERE `name` = '{$name}';
            COMMIT;
        ");
    }

    /**
     * @param $name
     * @param $description
     * @param $user_id
     * @throws SystemException
     */
    public function createLeague($name, $description, $user_id)
    {
        $leader_key = ROLE::LEADER;
        $this->db->multi_query("
            START TRANSACTION;
            INSERT INTO `{$this->table}` (`name`, `description`) VALUES ('{$name}', '{$description}');
            UPDATE `users`
                SET `users`.`league_id` = (SELECT id FROM `{$this->table}` WHERE `name` = '{$name}'),
                    `users`.`role_id` = (SELECT id FROM `roles` WHERE `key` = '{$leader_key}')
                WHERE `users`.`id` = {$user_id};
            COMMIT;
         ");
    }

    /**
     * @param string $name
     * @param string $description
     * @throws SystemException
     */
    public function changeDescription(string $name, string $description)
    {
        $this->db->query("
            UPDATE `{$this->table}` SET `description` = '{$description}' WHERE `name` = '{$name}';
        ");
        return;
    }
}
