<?php
namespace Classes\Validators;

use Classes\DbConnection;
use Classes\League;
use Classes\Request;
use Classes\User;
use Classes\Role;

/**
 * Class LeagueDeleteValidator
 * @package Classes\Validators
 */
class LeagueDeleteValidator extends Validator
{
    public function __construct(Request $request, DbConnection $db)
    {
        parent::__construct($request, $db);
    }

    /**
     * @return array
     */
    public function validate()
    {
        $user = $this->getValidUser('user_id');

        if ($user && $user['role_key'] !== ROLE::LEADER) {
            $this->errors[] = 'Only user with the role ' . ROLE::LEADER. ' can delete the League.';
        }

        if (!$this->request->league_name) {
            $this->errors[] = 'league_name is required';
        }

        if ($user && $this->request->league_name && $user['league_name'] !== $this->request->league_name) {
            $this->errors[] = 'Only member of '
                . $this->request->league_name
                . ' can delete the League.';
        }

        $leagueObj = new League($this->db);
        $league = $leagueObj->getLeagueByName($this->request->league_name);
        if (!$league) {
            $this->errors[] = 'The League with the name ' . $this->request->league_name . ' not found.';
        }

        return $this->errors;
    }

}
