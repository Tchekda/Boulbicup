<?php


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\User;
use Service\App;
use Service\MatchGenerator;

require '../vendor/autoload.php';

class MatchController extends BaseController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var \AltoRouter
     */
    private $router;

    /**
     * MatchController constructor, stores the router instance and the entity manager from the index router.
     * @param $router
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($router, EntityManagerInterface $entityManager)
    {
        parent::__construct($router, $entityManager);
        $this->entityManager = $entityManager;
        $this->router = $router;

        if (!App::is_loggedin($this->entityManager)) { // If visitor is not loggedin
            header('Location: ' . $this->router->generate('admin_login')); // Redirect to login page
            exit();
        }
    }


    public function generateMatchs(string $id){
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        $matchGenerator = new MatchGenerator($this->entityManager, $tournament);

        $matchs = $matchGenerator->generateTournamentMatchs();

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()])); // Redirect to tournament's edit page
        exit();
    }


}