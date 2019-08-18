<?php


use Controller\HomeController;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

// Require composer dependencies
require_once "../vendor/autoload.php";

// Start PHP Session
session_start();

//-------------------------------- DATABASE --------------------------------
// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = (getenv('env') ? getenv('env') : false);
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src/Entity"), $isDevMode);


// Setting up Database configuration parameters
if (!getenv('DATABASE_URL')){ // Check if env is defined
    die("No DATABASE_URL variable set in the environment");
}

$connectionParams = array(
    'url' => getenv('DATABASE_URL')
);

// Obtaining the entity manager
/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
try {
    $entityManager = EntityManager::create($connectionParams, $config);
} catch (ORMException $e) {
    die("Can't create Database connection");
}

//-------------------------------- ROUTER --------------------------------
// Setting up the router
$router = new AltoRouter();

// Define all routes
try {
    $router->addRoutes(array(
        array('GET', '/', array(HomeController::class, 'homepage'), 'homepage'),
        array('GET', '/contact', array(HomeController::class, 'contact'), 'contact'),
        array('GET', '/tournament/[i:id]', array(HomeController::class, 'homepage'), 'tournament'),
    ));
} catch (Exception $e) {
    die("Can't register routes : " . $e->getMessage());
}

// Try to find the corresponding route
if ($match = $router->match()) { // If a route is found
    $controller = new $match['target'][0]($router); // Init the controller
    call_user_func_array(array($controller, $match['target'][1]), $match['params']); // Call the corresponding closure
}else { // Route Not Found
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    // TODO Make 404 page
}