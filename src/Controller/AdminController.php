<?php


namespace Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Pool;
use Entity\Team;
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
        $tournaments = $this->entityManager->getRepository("Entity\\Tournament")->findAll();
        echo $this->twig->render('admin/lists/tournaments.html.twig', ['tournaments' => $tournaments]);
    }

    public function tournamentNew(){
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        echo $this->twig->render('admin/manage/tournament/tournament_new.html.twig');
    }

    public function tournamentNewForm(){
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }elseif (empty($_POST)){
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }

        if ($id = intval($_POST['tournament_id'])){
            if (!$tournament = $this->entityManager->getRepository('Entity\\Tournament')->find($id)){
                $tournament = new Tournament();
            }
        }else {
            $tournament = new Tournament();
        }

        $tournament->setName($_POST['name']);
        $tournament->setCategory(intval($_POST['category']));

        $start_datetime = DateTime::createFromFormat('d/m/Y H:i', $_POST['start_date'] . ' ' . $_POST['start_time']);
        $end_datetime = DateTime::createFromFormat('d/m/Y H:i', $_POST['end_date'] . ' ' . $_POST['end_time']);
        if ($start_datetime < $end_datetime){
            $tournament->setStartDatetime($start_datetime);
            $tournament->setEndDatetime($end_datetime);
            if ($tournament->getId()){
                $this->entityManager->flush();
                header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()]));
            }else {
                $this->entityManager->persist($tournament);
                $this->entityManager->flush();

                header('Location: ' . $this->router->generate('admin_tournament_list'));
                exit();
            }
        }else {
            if ($tournament->getId()){
                header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()]));
            }else {
                header('Location: ' . $this->router->generate('admin_tournament_new'));
            }
        }

    }

    public function tournamentEdit($id) {
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        /** @var Tournament $tournament */
        if (!$tournament = $this->entityManager->getRepository('Entity\\Tournament')->find($id)){
            header('Location: ' . $this->router->generate('admin_index'));
        }

        echo $this->twig->render('admin/manage/tournament/tournament_edit.html.twig', ['tournament' => $tournament]);
    }

    public function teamNew($id) {
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        /** @var Tournament $tournament */
        if (!$tournament = $this->entityManager->getRepository('Entity\\Tournament')->find($id)){
            header('Location: ' . $this->router->generate('admin_index'));
        }


        echo $this->twig->render('admin/manage/team/team_new.html.twig', ['tournament' => $tournament]);

    }

    public function teamNewForm($id){
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        /** @var Tournament $tournament */
        if (!$tournament = $this->entityManager->getRepository('Entity\\Tournament')->find($id)){
            header('Location: ' . $this->router->generate('admin_index'));
        }

        unset($_POST['action']);
        $pools = array();
        foreach ($_POST as $key => $value) {
            preg_match('/^pool_(\d)_name$/', $key, $poolMatches);
            if (count($poolMatches) > 0){
                $poolID = intval($poolMatches[1]);
                $pools[$poolID] = [
                    'name' => $value,
                    'teams' => array()
                ];
            }else{
                preg_match('/^pool_(\d)_name_(\d)$/', $key, $teamMatches);
                if (count($teamMatches) > 1){
                    $pools[intval($teamMatches[1])]['teams'][] = $value;
                }
            }
        }


        dd($tournament);

        foreach ($pools as $poolName => $poolTeams){
            /** @var $pool Pool */
            if (null == $pool = $tournament->getPool($poolName)){
                $pool = new Pool();
                $pool->setName($poolName);
                $pool->setTournament($tournament);

                $tournament->addPool($pool);

                $this->entityManager->persist($pool);
            }

            foreach ($poolTeams as $poolTeam){
                if (null == $team = $pool->getTeam($poolTeam)){
                    $team = new Team();
                    $team->setName($poolTeam);
                    $team->setPool($pool);
                    $team->setTournament($tournament);

                    $this->entityManager->persist($team);

                    $pool->addTeam($team);
                    $tournament->addTeam($team);
                }
            }
            $this->entityManager->flush();
        }

        dd($tournament);

    }

    public function userList(){
        if (!App::is_loggedin($this->entityManager)) {
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        }
        /** @var User[] $users */
        $users = $this->entityManager->getRepository("Entity\\User")->findAll();
        echo $this->twig->render('admin/lists/users.html.twig', ['users' => $users]);
    }
}