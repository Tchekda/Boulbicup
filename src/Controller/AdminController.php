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
     * Tournament list page, displays all registered tournament to edit/delete them "/admin/tournaments"
     */
    public function tournamentList() {
        /** @var Tournament[] $tournaments */
        $tournaments = $this->entityManager->getRepository("Entity\\Tournament")->findAll(); // Get all tournament registered
        echo $this->twig->render('admin/lists/tournaments.html.twig', ['tournaments' => $tournaments]);
    }

    /**
     * New tournament form request function, "GET:/admin/tournament/new"
     */
    public function tournamentNew() {
        echo $this->twig->render('admin/manage/tournament/tournament_new.html.twig');
    }

    /**
     * Tournament submission function (New or Edit), "POST:/admin/tournament/submit"
     */
    public function tournamentSubmitForm() {
        if (empty($_POST)) { // If nothing is posted
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        } elseif (!isset($_POST['name']) || !isset($_POST['category']) || !isset($_POST['date_first'])
            || !isset($_POST['start_time_first']) || !isset($_POST['end_time_first'])) { // If missing one of the required field
            $notifications['red'] = "Vous n'avez pas rempli tous les champs obligatoires"; // Display a notification
            if (isset($_POST['tournament_id'])) { // If tournament is edited
                header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => intval($_POST['tournament_id'])])); // Redirect to edit page
                exit();
            }else {
                echo $this->twig->render('admin/manage/tournament/tournament_new.html.twig', ['notifications' => $notifications]); // Display the new tournament form
                exit();
            }
        }

        $tournament = new Tournament();
        if (isset($_POST['tournament_id'])) { // If tournament is edited, the tournament ID is submited
            if ($id = intval($_POST['tournament_id'])) { // If value can be converted to int
                /** @var Tournament $tournamentRequest */
                if ($tournamentRequest = $this->entityManager->getRepository('Entity\\Tournament')->find($id)) { // If tournament can be find by given ID
                    $tournament = $tournamentRequest; // Replace empty Tournament by the existing one
                }
            }
        }

        // Filling general data
        $tournament->setName($_POST['name']);
        $tournament->setCategory(intval($_POST['category']));

        // Convert string dates to DateTime objects
        $start_datetime_first = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_first'] . ' ' . $_POST['start_time_first']);
        $end_datetime_first = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_first'] . ' ' . $_POST['end_time_first']);
        if (isset($_POST['date_second'])) { // If a second date is submitted
            $start_datetime_second = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_second'] . ' ' . $_POST['start_time_second']);
            $end_datetime_second = DateTime::createFromFormat('d/m/Y H:i', $_POST['date_second'] . ' ' . $_POST['end_time_second']);
            if ($end_datetime_first < $start_datetime_second) { // If the second day is after the first (valid)
                // Filling the second day values
                $tournament->setStartDatetimeSecondDay($start_datetime_second);
                $tournament->setEndDatetimeSecondDay($end_datetime_second);
            }
        }
        // Filling first day values
        $tournament->setStartDatetimeFirstday($start_datetime_first);
        $tournament->setEndDatetimeFirstday($end_datetime_first);

        if (!$tournament->getId()) { // If tournament do not already exists (New)
            $this->entityManager->persist($tournament);
        }
        $this->entityManager->flush();

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()])); // Redirect to tournament's edit page
        exit();


    }

    /**
     * @param string $id Tournament ID
     * Tournament edit request function, displays tournament management page "/admin/tournament/edit/[i:id]"
     */
    public function tournamentEdit(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        echo $this->twig->render('admin/manage/tournament/tournament_edit.html.twig', ['tournament' => $tournament]);
    }

    /**
     * @param string $id Tournament ID that the teams relate to
     * New team request function, displays the form to add/edit teams "GET:/admin/teams/new/[i:id]"
     */
    public function teamNew(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        echo $this->twig->render('admin/manage/team/team_new.html.twig', ['tournament' => $tournament]);

    }

    /**
     * @param string $id Tournament ID that the teams relate to
     * New team submission form, converts all posted data to entity objects "POST:/admin/teams/new/[i:id]"
     */
    public function teamNewForm(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        unset($_POST['action']); // Remove 'action' from the array so there is only needed data
        $pools = array(); // Init pools array with teams
        foreach ($_POST as $field_name => $field_value) {
            preg_match('/^pool_(\d)_name$/', $field_name, $poolMatches);
            if (count($poolMatches) > 0) { // If the field name is a pool name
                $poolID = intval($poolMatches[1]);
                $pools[$poolID] = [ // Create Pool entry in the pools array
                    'name' => $field_value,
                    'teams' => array()
                ];
            } else {
                preg_match('/^pool_(\d)_name_(\d)$/', $field_name, $teamMatches);
                if (count($teamMatches) > 1) { // If the field name is a team name
                    if (isset($pools[intval($teamMatches[1])])){ // If the team's pool is in the array
                        $pools[intval($teamMatches[1])]['teams'][] = $field_value; // Add the team to the related pool
                    } // If not, do nothing
                }
            }
        }


        foreach ($pools as $poolArray) { // Convert the $pools array to Pool objects
            $poolName = $poolArray['name'];
            $poolTeams = $poolArray['teams'];
            /** @var $pool Pool */
            if (null == $pool = $tournament->getPool($poolName)) { // If this Pool do not exist in this tournament
                $pool = new Pool();
                $pool->setName($poolName);
                $pool->setTournament($tournament);

                $tournament->addPool($pool);

            }

            foreach ($poolTeams as $poolTeam) { // Convert the $poolTeams array to Team objects
                if (null == $team = $pool->getTeam($poolTeam)) { // If this Team do not exist in this pool
                    $team = new Team();
                    $team->setName($poolTeam);
                    $team->setPool($pool);
                    $team->setTournament($tournament);

                    $this->entityManager->persist($team);

                    $pool->addTeam($team);
                    $tournament->addTeam($team);

                }
            }
            $this->entityManager->persist($pool);

            $this->entityManager->persist($tournament);
            $this->entityManager->flush();
        }

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()])); // Redirect to Tournament's edit page
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
     * @param string $id
     * @return Tournament
     * Function to try to find a tournament by an ID given in the URL
     */
    private function findTournamentByID(string $id): Tournament{
        $id = intval($id); // Convert string ID to integer
        /** @var Tournament $tournament */
        if (!$tournament = $this->entityManager->getRepository('Entity\\Tournament')->find($id)) { // If tournament can't be find
            header('Location: ' . $this->router->generate('admin_tournament_list')); // Redirect to tournaments list
            exit();
        }
        return $tournament;
    }
}