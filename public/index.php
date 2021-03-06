<?php

use Controller\AdminController;
use Controller\HomeController;
use Controller\MatchController;
use Controller\TeamController;
use Controller\TournamentController;


// Require composer dependencies
require_once "../vendor/autoload.php";
require_once '../bootstrap.php';

// Set timezone to Paris
date_default_timezone_set('Europe/Paris');


// Start PHP Session
session_start();


//-------------------------------- ROUTER --------------------------------
// Setting up the router
$router = new AltoRouter();

// Define all routes
try {
    $router->addRoutes(array(
        // Home
        array('GET', '/', array(HomeController::class, 'homepage'), 'homepage'),
        array('GET', '/tournament/[i:id]', array(HomeController::class, 'tournamentShow'), 'tournament_show'),
        array('GET', '/login', array(HomeController::class, 'login'), 'admin_login'),
        array('POST', '/login', array(HomeController::class, 'loginForm'), 'admin_login_form'),
        // Admin
        array('GET', '/admin/', array(AdminController::class, 'index'), 'admin_index'),
        array('GET', '/admin/logout', array(AdminController::class, 'logout'), 'admin_logout'),
        array('GET', '/admin/users', array(AdminController::class, 'userList'), 'admin_user_list'),
        // Tournament
        array('GET', '/admin/tournaments', array(TournamentController::class, 'tournamentList'), 'admin_tournament_list'),
        array('GET', '/admin/tournament/new', array(TournamentController::class, 'tournamentNew'), 'admin_tournament_new'),
        array('POST', '/admin/tournament/submit', array(TournamentController::class, 'tournamentSubmitForm'), 'admin_tournament_new_form'),
        array('GET', '/admin/tournament/edit/[i:id]', array(TournamentController::class, 'tournamentEdit'), 'admin_tournament_edit'),
        // Team
        array('GET', '/admin/teams/new/[i:id]', array(TeamController::class, 'teamNew'), 'admin_team_new'),
        array('POST', '/admin/teams/new/[i:id]', array(TeamController::class, 'teamNewForm'), 'admin_team_new_form'),
        // Match
        array('GET', '/admin/matchs/pool/generate/[i:id]', array(MatchController::class, 'generatePoolMatchs'), 'admin_pool_match_generate'),
        array('GET', '/admin/matchs/ranking/generate/[i:id]', array(MatchController::class, 'generateRankingMatchs'), 'admin_ranking_match_generate'),
        // AJAX
        array('GET', '/ajax/tournament/update/[i:id]', array(HomeController::class, 'ajaxTournamentUpdate'), 'ajax_tournament_update'),
        array('POST', '/ajax/admin/tournament/delete/[i:id]', array(TournamentController::class, 'ajaxTournamentDelete'), 'ajax_admin_tournament_delete'),
        array('POST', '/ajax/admin/team/delete/[i:id]', array(TeamController::class, 'ajaxTeamDelete'), 'ajax_admin_team_delete'),
        array('POST', '/ajax/admin/pool/delete/[i:id]', array(TeamController::class, 'ajaxPoolDelete'), 'ajax_admin_pool_delete'),
        array('POST', '/ajax/admin/user/add', array(AdminController::class, 'ajaxUserAdd'), 'ajax_admin_user_add'),
        array('POST', '/ajax/admin/user/delete/[i:id]', array(AdminController::class, 'ajaxUserDelete'), 'ajax_admin_user_delete'),
        array('POST', '/ajax/admin/matchs/pool/edit/[i:id]', array(MatchController::class, 'ajaxPoolMatchEdit'), 'ajax_admin_pool_match_edit'),
        array('POST', '/ajax/admin/matchs/recalculate/[i:id]', array(MatchController::class, 'ajaxRecalcutatePoints'), 'ajax_admin_match_recalculate'),
    ));
} catch (Exception $e) {
    die("Can't register routes : " . $e->getMessage());
}

// Try to find the corresponding route
if ($match = $router->match()) { // If a route is found
    $controller = new $match['target'][0]($router, $entityManager); // Init the controller
    call_user_func_array(array($controller, $match['target'][1]), $match['params']); // Call the corresponding closure
} else { // Route Not Found
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    $controller = new HomeController($router, $entityManager);
    $controller->notFound();
}