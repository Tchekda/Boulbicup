<?php


namespace Controller;


use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class BaseController {

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
     * BaseController constructor.
     * @param $router \AltoRouter
     */
    public function __construct($router, EntityManagerInterface $entityManager) {
        $loader = new FilesystemLoader('../templates'); // Load templates folder
        $this->twig = new Environment($loader);
        $this->router = $router;

        $path_function = new TwigFunction('path', function (string $name, array $params = array()) { // Add path generator function
            return $this->router->generate($name, $params);
        });
        $this->twig->addFunction($path_function);

        $this->twig->addExtension(new \Twig_Extensions_Extension_Intl());


        $this->entityManager = $entityManager;

        $this->setTemplateParams();
    }

    private function setTemplateParams(){
        $tournaments = $this->entityManager->getRepository("Entity\\Tournament")->findAll();
        $this->template_params['tournaments'] = $tournaments;
    }

}