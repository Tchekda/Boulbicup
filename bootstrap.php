<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

require_once "vendor/autoload.php";

//-------------------------------- DATABASE --------------------------------
// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src/Entity"), $isDevMode);


// Setting up Database configuration parameters
if (!getenv('DATABASE_URL')){ // Check if env is defined
    die("No DATABASE_URL variable set in the environment");
}
$connectionParams = array(
    'url' => getenv('DATABASE_URL'),
    'driver' => 'pdo_mysql'
);

// Obtaining the entity manager
/** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
try {
    $entityManager = EntityManager::create($connectionParams, $config);
} catch (ORMException $e) {
    die("Can't create Database connection");
}
