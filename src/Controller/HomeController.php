<?php


namespace Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require '../vendor/autoload.php';

class HomeController extends BaseController {

    public function __construct($router) {
        parent::__construct($router);
    }

    public function homepage() {
        echo $this->twig->render('home/index.html.twig');
    }


}