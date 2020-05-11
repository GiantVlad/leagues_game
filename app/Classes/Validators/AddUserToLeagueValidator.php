<?php

namespace Classes\Validators;

use Classes\DbConnection;
use Classes\League;
use Classes\Request;
use Classes\Role;


/**
 * Class AddUserToLeagueValidator
 * @package Classes\Validators
 */
class AddUserToLeagueValidator extends Validator
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
        $current_user = $this->getValidUser('current_user_id');

        if ( $current_user && $current_user['league_name'] !== $this->request->league_name) {
            $this->errors[] = 'Only user from the League '
                . $this->request->league_name . ' can add target user to this League.';
        }

        if ( $current_user && $current_user['role_key'] !== ROLE::LEADER) {
            $this->errors[] = 'Only user with role '
                . ROLE::LEADER . ' can add target user to a League.';
        }

        if ( $user && $user['role_key'] ) {
            $this->errors[] = 'Target user is already in a League.';
        }

        if (!$this->request->league_name) {
            $this->errors[] = 'league_name is required.';
        } else {
            $leagueObj = new League($this->db);
            $league = $leagueObj->getLeagueByName($this->request->league_name);

            if (!$league) {
                $this->errors[] = 'The league with the name ' . $this->request->league_name . ' not found.';
            }
        }

        return $this->errors;
    }
}
