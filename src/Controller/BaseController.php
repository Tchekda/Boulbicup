<?php


namespace Controller;


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
     * BaseController constructor.
     * @param $router \AltoRouter
     */
    public function __construct($router) {
        $loader = new FilesystemLoader('../templates'); // Load templates folder
        $this->twig = new Environment($loader);
        $this->router = $router;

        $path_function = new TwigFunction('path', function (string $name, array $params = array()) { // Add path generator function
            return $this->router->generate($name, $params);
        });

        $this->twig->addFunction($path_function);

    }

}