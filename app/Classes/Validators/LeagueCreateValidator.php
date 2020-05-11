<?php
namespace Classes\Validators;

use Classes\DbConnection;
use Classes\League;
use Classes\Request;

/**
 * Class LeagueCreateValidator
 * @package Classes\Validators
 */
class LeagueCreateValidator extends Validator
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
        if ($user && $user['role_key']) {
            $this->errors[] = "User with ID '{$user['id']}' is already in the league.";
        }

        if (!$this->request->league_name) {
            $this->errors[] = 'league_name is required';
        }

        if ($this->request->league_name && !$this->checkStringLength($this->request->league_name, 1, 12)) {
            $this->errors[] = 'The length of league_name should be between 1 and 12 chars.';
        }

        if ($this->request->league_name
            && $this->request->league_name
                !== preg_replace('/[^A-Za-z0-9\-]/', '', $this->request->league_name)
        ) {
            $this->errors[] = 'The league_name should\'t contain special chars or spaces.';
        }

        if ($this->request->league_description !== false
            && !$this->checkStringLength($this->request->league_description, 0, 30)
        ) {
            $this->errors[] = 'The length of description should be between 0 and 30 chars.';
        }

        if ($this->request->league_name) {
            $leagueObj = new League($this->db);
            $league = $leagueObj->getLeagueByName($this->request->league_name);
            if ($league) {
                $this->errors[] = 'The league with the name ' . $this->request->league_name . ' already exists.';
            }
        }

        return $this->errors;
    }

}
