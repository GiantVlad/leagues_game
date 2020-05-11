<?php
namespace Classes\Validators;

use Classes\DbConnection;
use Classes\League;
use Classes\Request;
use Classes\Role;
use Classes\User;

/**
 * Class RemoveUserFromLeagueValidator
 * @package Classes\Validators
 */
class RemoveUserFromLeagueValidator extends Validator
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

        if ($current_user && $current_user['league_name'] !== $this->request->league_name) {
            $this->errors[] = 'Only user from the League '
                . $this->request->league_name . ' can delete target user from this League.';
        }

        if ($current_user && $current_user['role_key'] !== ROLE::LEADER) {
            $this->errors[] = 'Only user with role '
                . ROLE::LEADER . ' can delete target user from this League.';
        }

        if ( $user && $user['league_name'] !== $this->request->league_name ) {
            $this->errors[] = 'Target user not in this League ' . $this->request->league_name;
        }

        if ( $user && $user['role_key'] !== ROLE::SOLDER ) {
            $this->errors[] = 'Target user should be a ' . ROLE::SOLDER;
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
