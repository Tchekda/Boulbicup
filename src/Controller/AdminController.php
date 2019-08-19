<?php


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\User;
use Service\App;

require '../vendor/autoload.php';

class AdminController extends BaseController {

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var \AltoRouter
     */
    private $router;

    public function __construct($router, EntityManagerInterface $entityManager) {
        parent::__construct($router);
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function index(){
        echo $this->twig->render('admin/index.html.twig');
    }

    public function login() {
        var_dump(App::is_loggedin($this->entityManager));
        echo $this->twig->render('admin/login.html.twig');
    }

    public function loginForm() {
        /** @var User $user */
        if ($user = $this->entityManager->getRepository('Entity\\User')->findOneBy(['username' => $_POST['username']])){
            if (password_verify($_POST['password'], $user->getPassword())){
                $_SESSION['auth'] = $user->getId();
                $_SESSION['logged'] = true;
                header('Location: ' . $this->router->generate('admin_index'));
                exit();
            }else {
                $notifications['red'] = 'Mot de passe invalide';
            }
        }else {
            $notifications['red'] = 'Compte introuvable';
        }
        echo $this->twig->render('admin/login.html.twig', ['notifications' => $notifications]);
    }
}