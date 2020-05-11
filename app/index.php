<?php

use Classes\DbConnection;
use Classes\League;

include_once 'autoloader.php';

$db = DbConnection::getInstance();

$leagueObj = new League($db);
try {
    $leagues = $leagueObj->getLeaguesWithUsers();
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode($e->getMessage());
    exit;
}

echo json_encode($leagues);
