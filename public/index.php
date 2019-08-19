<?php

use Controller\AdminController;
use Controller\HomeController;


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
        array('GET', '/', array(HomeController::class, 'homepage'), 'homepage'),
        array('GET', '/contact', array(HomeController::class, 'contact'), 'contact'), // TODO Contact Page
        array('GET', '/tournament/[i:id]', array(HomeController::class, 'homepage'), 'tournament'), // TODO Tournament Page
        array('GET', '/admin/', array(AdminController::class, 'index'), 'admin_index'),
        array('GET', '/admin/login', array(AdminController::class, 'login'), 'admin_login'),
        array('POST', '/admin/login', array(AdminController::class, 'loginForm'), 'admin_login_form'),
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
    // TODO Make 404 page
}