<?php


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\Tournament;
use Entity\User;
use Service\App;
use Service\MatchGenerator;
use Service\Ranking;

require '../vendor/autoload.php';

class HomeController extends BaseController {

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \AltoRouter
     */
    private $router;

    /**
     * HomeController constructor, stores the router and the entity manager from the index router.
     * @param $router
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($router, EntityManagerInterface $entityManager) {
        parent::__construct($router, $entityManager);
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * Homepage function that displays the homepage "/" and getting all future tournaments to display in the paragraph
     */
    public function homepage() {
        $future_tournaments = $this->entityManager->getRepository('Entity\\Tournament')->findFutureTournaments();
        $template_params = array_merge($this->template_params, ['future_tournaments' => $future_tournaments]);
        echo $this->twig->render('home/index.html.twig', $template_params);
    }

    /**
     * @param string $tournamentID
     * Displays all data about a specific tournament
     */
    public function tournamentShow(string $tournamentID) {
        /** @var Tournament $tournament */
        if ($tournament = $this->entityManager->getRepository('Entity\\Tournament')->find(intval($tournamentID))) {
            $ranking = new Ranking($tournament);
            $matchs = array();
            foreach ($tournament->getMatchs() as $match) {
                $matchs[$match->getTime()->format('l j')][] = $match;
            }
            $ranked_teams = $ranking->getStandardisedData();
//            dd($ranked_teams);
            echo $this->twig->render('home/tournament.html.twig', [
                'tournaments' => $this->entityManager->getRepository('Entity\\Tournament')->findAll(),
                'tournament' => $tournament,
                'ranked_teams' => $ranked_teams,
                'matchs' => $matchs
            ]);
        } else {
            header('Location: ' . $this->router->generate('homepage'));
        }
    }

    /**
     * @param string $tournamentID
     * Gives all needed data in JSON about a tournament
     */
    public function ajaxTournamentUpdate(string $tournamentID){
        /** @var Tournament $tournament */
        if ($tournament = $this->entityManager->getRepository('Entity\\Tournament')->find(intval($tournamentID))) {
            $matchGenerator = new MatchGenerator($this->entityManager, $tournament);
            $matchGenerator->recalculatePoints();

            $ranking = new Ranking($tournament);
            $ranked_teams = $ranking->getStandardisedData();

            $matchs = array();
            foreach ($tournament->getMatchs() as $match){
                $matchs[] = MatchGenerator::standardiseMatchData($match);;
            }

            $state = $tournament->getStateName();

            $data = [
               'matchs' => $matchs,
               'ranking' => $ranked_teams,
               'state' => $state
            ];


            header('HTTP/1.0 200 OK');
            header('Content-Type: application/json');
            echo json_encode($data);

        } else {
            header('HTTP/1.0 404 Not Found');
            echo 'Tournament Not Found';
            exit();
        }
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
        } elseif (empty($_POST)) {
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

    /**
     * Page that will be displayed en 404 Errors (Source: https://codepen.io/sqfreakz/pen/GJRJOY )
     */
    public function notFound() {
        echo $this->twig->render('home/404.html.twig');
    }


}