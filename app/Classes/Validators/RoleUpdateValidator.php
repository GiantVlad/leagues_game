<?php
namespace Classes\Validators;

use Classes\DbConnection;
use Classes\League;
use Classes\Request;
use Classes\Role;
use Classes\User;

/**
 * Class RoleUpdateValidator
 * @package Classes\Validators
 */
class RoleUpdateValidator extends Validator
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

        if ($user && $current_user && $current_user['league_name'] !== $user['league_name'] ) {
            $this->errors[] = 'Current user and target user should be in the same League.';
        }

        if ($current_user && !in_array($current_user['role_key'], [ROLE::LEADER, ROLE::ALTERNATE]) ) {
            $this->errors[] = 'Only users with role ' . ROLE::LEADER . ' or ' . ROLE::ALTERNATE . ' can change the role.';
        }

        if (!$this->request->role) {
            $this->errors[] = 'The role field is required.';
        }

        if ($this->request->role && !in_array($this->request->role, [ROLE::LEADER, ROLE::ALTERNATE, ROLE::SOLDER])) {
            $this->errors[] = 'Invalid key of the role.' . $this->request->role;
        }

        if ( $this->request->role &&  $this->request->role === ROLE::LEADER) {
            $this->errors[] = 'Forbidden to set role ' . ROLE::LEADER . '. You can just create a new League.';
        }

        if ( $this->request->role && $current_user && $user
            &&  $this->request->role === ROLE::ALTERNATE
            && (!in_array($current_user['role_key'], [ROLE::ALTERNATE, ROLE::LEADER])
                || $user['role_key'] === ROLE::LEADER
            )
        ) {
            $this->errors[] = 'Forbidden. Not enough permissions to set role.' . $this->request->role;
        }

        return $this->errors;
    }
}
