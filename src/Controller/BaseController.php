<?php


namespace Controller;


use Doctrine\ORM\EntityManagerInterface;
use Entity\Tournament;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class BaseController {

    /**
     * @var Environment
     */
    public $twig;
    /**
     * @var \AltoRouter
     */
    private $router;

    /**
     * @var array
     */
    public $template_params = array();
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BaseController constructor that configs Twig and generates and required template variables.
     * @param $router \AltoRouter
     */
    public function __construct($router, EntityManagerInterface $entityManager) {
        $loader = new FilesystemLoader('../templates'); // Load templates folder
        $this->twig = new Environment($loader);
        $this->router = $router;

        $path_function = new TwigFunction('path', function (string $name, array $params = array()) { // Add path generator function
            return $this->router->generate($name, $params);
        });

        $id_dev_function = new TwigFunction('is_dev', function () { // Add path generator function
            return getenv('dev', true) ?: getenv('dev');
        });

        $this->twig->addFunction($path_function);
        $this->twig->addFunction($id_dev_function);


        $this->twig->addExtension(new \Twig_Extensions_Extension_Intl());

        $this->entityManager = $entityManager;

        $this->setTemplateParams();
    }

    private function setTemplateParams(){
        $tournaments = $this->entityManager->getRepository("Entity\\Tournament")->findAll();
        $this->template_params['tournaments'] = $tournaments;
    }

    /**
     * @param string $id
     * @return Tournament
     * Function to try to find a tournament by an ID given in the URL
     */
    public function findTournamentByID(string $id): Tournament
    {
        $id = intval($id); // Convert string ID to integer
        /** @var Tournament $tournament */
        if (!$tournament = $this->entityManager->getRepository('Entity\\Tournament')->findByID($id)) { // If tournament can't be find
            header('Location: ' . $this->router->generate('admin_tournament_list')); // Redirect to tournaments list
            exit();
        }
        return $tournament;
    }
}