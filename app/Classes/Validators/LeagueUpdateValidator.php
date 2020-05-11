<?php
namespace Classes\Validators;

use Classes\DbConnection;
use Classes\League;
use Classes\Request;
use Classes\Role;


/**
 * Class LeagueUpdateValidator
 * @package Classes\Validators
 */
class LeagueUpdateValidator extends Validator
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

        if ( !in_array($user['role_key'], [ROLE::LEADER, ROLE::ALTERNATE]) ) {
            $this->errors[] = 'Only users with role '
                . ROLE::LEADER . ' or '
                . ROLE::ALTERNATE . ' can update the League\'s description.';
        }

        if ( $user['league_name'] !== $this->request->league_name ) {
            $this->errors[] = 'Only member of '
                . $this->request->league_name
                . ' League can update the League\'s description.';
        }

        if (!$this->request->league_name) {
            $this->errors[] = 'league_name is required.';
        }

        $leagueObj = new League($this->db);
        $league = $leagueObj->getLeagueByName($this->request->league_name);

        if (!$league) {
            $this->errors[] = 'The League with the name ' . $this->request->league_name . ' not found.';
        }

        if ($this->request->league_description === false) {
            $this->errors[] = 'The league_description is required.';
        }

        if ($this->request->league_description
            && !$this->checkStringLength($this->request->league_description, 0, 30)
        ) {
            $this->errors[] = 'The length of description should be between 1 and 30 chars.';
        }
        return $this->errors;
    }
}
