<?php


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\User;
use Service\App;

require '../vendor/autoload.php';

class AdminController extends BaseController
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
     * AdminController constructor, stores the router instance and the entity manager from the index router.
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

    /**
     * Admin homepage functions "/admin/"
     */
    public function index()
    {
        $notifications = array();
        if (isset($_SESSION['logged'])) { // If just loggedin
            unset($_SESSION['logged']);
            $notifications['green'] = 'Vous êtes bien connecté'; // Display a notification
        }
        echo $this->twig->render('admin/index.html.twig', ['notifications' => $notifications]);
    }

    /**
     * Logout function "/admin/logout"
     */
    public function logout()
    {
        session_destroy();
        header('Location: ' . $this->router->generate('homepage'));
        exit();
    }


    /**
     * User list page function, displays a list of all registered users on the website
     */
    public function userList()
    {
        /** @var User[] $users */
        $users = $this->entityManager->getRepository("Entity\\User")->findAll(); // Retrieve all User objects registered in database
        echo $this->twig->render('admin/lists/users.html.twig', ['users' => $users]);
    }
}