<?php

use Classes\DbConnection;
use Classes\League;
use Classes\Request;
use Classes\SystemException;
use Classes\Validators\LeagueCreateValidator;
use Classes\Validators\LeagueDeleteValidator;
use Classes\Validators\LeagueUpdateValidator;

include_once 'autoloader.php';

$request = new Request();
$db = DbConnection::getInstance();
$leagueObj = new League($db);

try {
    if (!$request->action) {
        throw new SystemException('The field action is required.', 400);
    };

    if (!in_array($request->action, League::ACTIONS)) {
        throw new SystemException('Invalid action.', 400);
    };

    switch ($request->action) {
        case 'create':
            $validator = new LeagueCreateValidator($request, $db);
            $errors = $validator->validate();
            if (!empty($errors)) {
                throw new SystemException(json_encode($errors), 400);
            }
            $leagueObj->createLeague(
                $request->league_name,
                $request->league_description ? $request->league_description : '',
                $request->user_id
            );
            break;
        case 'delete':
            $validator = new LeagueDeleteValidator($request, $db);
            $errors = $validator->validate();
            if (!empty($errors)) {
                throw new SystemException(json_encode($errors), 400);
            }
            $leagueObj->deleteLeague($request->league_name);
            break;
        case 'update':
            $validator = new LeagueUpdateValidator($request, $db);
            $errors = $validator->validate();
            if (!empty($errors)) {
                throw new SystemException(json_encode($errors), 400);
            }
            $leagueObj->changeDescription($request->league_name, $request->league_description);
            break;
    }
    echo json_encode('Done');
    exit;

} catch (SystemException $e) {
    http_response_code($e->getCode() ? $e->getCode() : 500);
    echo json_encode('Error. ' . ($e->getMessage() ? $e->getMessage() : 'Internal server error.'));
    exit;
}
