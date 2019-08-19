<?php


namespace Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Tournament;
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
        parent::__construct($router, $entityManager);
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function index() {
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        $notifications = array();
        if (isset($_SESSION['logged'])) {
            unset($_SESSION['logged']);
            $notifications['green'] = 'Vous êtes bien connecté';
        }
        echo $this->twig->render('admin/index.html.twig', ['notifications' => $notifications]);
    }

    public function login() {
        if (App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_index'));
            exit();
        }
        echo $this->twig->render('admin/login.html.twig');
    }

    public function loginForm() {
        if (App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_index'));
            exit();
        }elseif (empty($_POST)){
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        /** @var User $user */
        if ($user = $this->entityManager->getRepository('Entity\\User')->findOneBy(['username' => $_POST['username']])) {
            if (password_verify($_POST['password'], $user->getPassword())) {
                $_SESSION['auth'] = $user->getId();
                $_SESSION['logged'] = true;
                header('Location: ' . $this->router->generate('admin_index'));
                exit();
            } else {
                $notifications['red'] = 'Mot de passe invalide';
            }
        } else {
            $notifications['red'] = 'Compte introuvable';
        }
        echo $this->twig->render('admin/login.html.twig', ['notifications' => $notifications]);
    }

    public function logout() {
        if (App::is_loggedin($this->entityManager)) {
            session_destroy();
        }
        header('Location: ' . $this->router->generate('homepage'));
        exit();
    }

    public function tournamentList() {
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        /** @var Tournament[] $tournaments */
        $tournaments = $this->entityManager->getRepository("Entity\\Tournament")->findAll(true);
        echo $this->twig->render('admin/lists/tournaments.html.twig', ['tournaments' => $tournaments]);
    }

    public function tournamentNew(){
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        echo $this->twig->render('admin/manage/tournament.html.twig');
    }

    public function tournamentNewForm(){
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }elseif (empty($_POST)){
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }

        $tournament = new Tournament();
        $tournament->setName($_POST['name']);
        $tournament->setCategory(intval($_POST['category']));
        $start_datetime = new DateTime($_POST['start_date'] . $_POST['start_time']);
        $end_datetime = new DateTime($_POST['end_date'] . $_POST['end_time']);
        if ($start_datetime < $end_datetime){
            $tournament->setStartDatetime($start_datetime);
            $tournament->setEndDatetime($end_datetime);

            $this->entityManager->persist($tournament);
            $this->entityManager->flush();

            header('Location : ' . $this->router->generate('admin_tournament_list'));
            exit();
        }else {
            $notifications['red'] = 'Le second jour doit être après le 1er';
            echo $this->twig->render('admin/manage/tournament.html.twig', ['notifications' => $notifications, 'tournament' => $tournament]);
        }

    }
}