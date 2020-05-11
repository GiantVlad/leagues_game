<?php

use Classes\DbConnection;
use Classes\Request;
use Classes\Role;
use Classes\SystemException;
use Classes\User;
use Classes\Validators\AddUserToLeagueValidator;
use Classes\Validators\RemoveUserFromLeagueValidator;
use Classes\Validators\RoleUpdateValidator;

include_once 'autoloader.php';

$request = new Request();
$db = DbConnection::getInstance();
$roleObj = new Role($db);
$userObj = new User($db);

try {
    if (!$request->action) {
        throw new SystemException('The field action is required.', 400);
    };

    if (!in_array($request->action, USER::ACTIONS)) {
        throw new SystemException('Invalid action.', 400);
    };

    switch ($request->action) {
        case 'set_role':
            $validator = new RoleUpdateValidator($request, $db);
            $errors = $validator->validate();
            if (!empty($errors)) {
                throw new SystemException(json_encode($errors), 400);
            }
            $roleObj->setRole(
                $request->user_id,
                $request->role
            );
            break;
        case 'add_to_league':
            $validator = new AddUserToLeagueValidator($request, $db);
            $errors = $validator->validate();
            if (!empty($errors)) {
                throw new SystemException(json_encode($errors), 400);
            }
            $userObj->addUserToLeague(
                $request->user_id,
                $request->league_name
            );
            break;
        case 'remove_from_league':
            $validator = new RemoveUserFromLeagueValidator($request, $db);
            $errors = $validator->validate();
            if (!empty($errors)) {
                throw new SystemException(json_encode($errors), 400);
            }
            $userObj->removeUserFromLeague($request->user_id);
            break;
    }
    echo json_encode('Done');
    exit;

} catch (SystemException $e) {
    http_response_code($e->getCode() ? $e->getCode() : 500);
    echo json_encode('Error. ' . ($e->getMessage() ? $e->getMessage() : 'Internal server error.'));
    exit;
}
