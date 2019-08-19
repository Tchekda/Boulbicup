<?php


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require '../vendor/autoload.php';

class HomeController extends BaseController {

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct($router, EntityManagerInterface $entityManager) {
        parent::__construct($router, $entityManager);
        $this->entityManager = $entityManager;
    }

    public function homepage() {
        $future_tournaments = $this->entityManager->getRepository('Entity\\Tournament')->findFutureTournaments();
        $template_params = array_merge($this->template_params, ['future_tournaments' => $future_tournaments]);
        echo $this->twig->render('home/index.html.twig', $template_params);
    }


}