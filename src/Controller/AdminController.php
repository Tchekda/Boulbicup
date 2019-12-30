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

    /**
     * AdminController constructor, stores the router instance and the entity manager from the index router.
     * @param $router
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($router, EntityManagerInterface $entityManager) {
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
    public function index() {
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
    public function logout() {
        session_destroy();
        header('Location: ' . $this->router->generate('homepage'));
        exit();
    }


    /**
     * User list page function, displays a list of all registered users on the website
     */
    public function userList() {
        /** @var User[] $users */
        $users = $this->entityManager->getRepository("Entity\\User")->findAll(); // Retrieve all User objects registered in database
        echo $this->twig->render('admin/lists/users.html.twig', ['users' => $users]);
    }

    /**
     * Adds a new user to admin's list through AJAX POST
     */
    public function ajaxUserAdd() {
        if (!empty($_POST)) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $password_second = $_POST['password_second'];
            /** @var User $user */
            foreach ($this->entityManager->getRepository('Entity\\User')->findAll() as $user) {
                if (strtolower($user->getUsername()) == strtolower($username)) {
                    header('HTTP/1.0 409 Conflict');
                    echo 'Username already taken';
                    exit();
                }
            }
            if ($password == $password_second) {
                $user = new User();
                $user->setUsername($username);
                $user->setPassword(password_hash($password, PASSWORD_ARGON2ID));

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $user_data = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                ];
                header('HTTP/1.0 200 OK');
                header('Content-Type: application/json');
                echo json_encode($user_data);
            } else {
                header('HTTP/1.0 418 Password');
                echo 'Passwords should be the same';
                exit();
            }
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Post Data is empty';
            exit();
        }
    }

    /**
     * @param string $id
     * Deletes user through AJAX POST
     */
    public function ajaxUserDelete(string $id) {
        /** @var User $user */
        if ($user = $this->entityManager->getRepository('Entity\\User')->find(intval($id))){
            if ($user->getId() != intval($_SESSION['auth'])) { // Because loggedin in the constructor
                $this->entityManager->remove($user);
                $this->entityManager->flush();
                $user_data = [
                    'id' => intval($id),
                ];
                header('HTTP/1.0 200 OK');
                header('Content-Type: application/json');
                echo json_encode($user_data);
            }else {
                header('HTTP/1.0 403 Forbidden');
                echo "You can't delete your own account";
                exit();
            }
        }else {
            header('HTTP/1.0 404 Not Found');
            echo 'User Not Found';
            exit();
        }
    }
}