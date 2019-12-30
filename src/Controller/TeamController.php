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

class TeamController extends BaseController
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

//        if (!App::is_loggedin($this->entityManager)) { // If visitor is not loggedin
//            header('Location: ' . $this->router->generate('admin_login')); // Redirect to login page
//            exit();
//        }
    }

    /**
     * @param string $id Tournament ID that the teams relate to
     * New team request function, displays the form to add/edit teams "GET:/admin/teams/new/[i:id]"
     */
    public function teamNew(string $id)
    {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        echo $this->twig->render('admin/manage/team/team_new.html.twig', ['tournament' => $tournament]);

    }

    /**
     * @param string $id Tournament ID that the teams relate to
     * New team submission form, converts all posted data to entity objects "POST:/admin/teams/new/[i:id]"
     * There are some "echo" for debugging, will be removed in final release
     */
    public function teamNewForm(string $id)
    {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        unset($_POST['action']); // Remove 'action' from the array so there is only needed data
        $pools = array(); // Init pools array with teams
        foreach ($_POST as $field_name => $field_value) {
            preg_match('/^pool_(\d+)_name$/', $field_name, $poolMatches);
            if (count($poolMatches) > 0) { // If the field name is a pool name
                $poolID = intval($poolMatches[1]);
                $pools[$poolID] = [ // Create Pool entry in the pools array
                    'id' => $poolID,
                    'name' => $field_value,
                    'teams' => array()
                ];
                echo "Found Pool $field_name : $poolID\r\n";
            } else {
                preg_match('/^pool_(\d+)_team_(\d+)$/', $field_name, $teamMatches);
                if (count($teamMatches) > 1) { // If the field name is a team name
                    if (isset($pools[intval($teamMatches[1])])) { // If the team's pool is in the array
                        $teamID = $teamMatches[2];
                        $pools[intval($teamMatches[1])]['teams'][] = [
                            'id' => $teamID,
                            'name' => $field_value
                        ]; // Add the team to the related pool
                        echo "Found Team $field_name : $teamID\r\n";
                    }else {
                        echo "Found but no corresponding pool $field_name : $field_value\r\n";
                    } // If not, do nothing
                }else {
                    echo "Found but no corresponding regex $field_name : $field_value\r\n";
                }
            }
        }


        foreach ($pools as $poolArray) { // Convert the $pools array to Pool objects
            $poolID = $poolArray['id'];
            $poolName = $poolArray['name'];
            $poolTeams = $poolArray['teams'];
            /** @var $pool Pool */
            if (null == $pool = $tournament->getPoolID($poolID)) { // If this Pool do not exist in this tournament
                $pool = new Pool();
                $pool->setTournament($tournament);
                echo "Pool not found, Created: $poolName\r\n";
                $tournament->addPool($pool);
            }else {
                echo "Pool found, Updated: $poolName\r\n";
            }
            $pool->setName($poolName);

            foreach ($poolTeams as $poolTeam) { // Convert the $poolTeams array to Team objects
                $teamID = $poolTeam['id'];
                $teamName = $poolTeam['name'];
                if (null == $team = $pool->getTeamID($teamID)) { // If this Team do not exist in this pool
                    $team = new Team();
                    $team->setPool($pool);
                    $team->setTournament($tournament);

                    echo "Team not found, Created: $teamName\r\n";


                    $pool->addTeam($team);
                    $tournament->addTeam($team);
                }else {
                    echo "Team found, Updated: $teamName\r\n";
                }
                $team->setName($teamName);
                $this->entityManager->persist($team);
            }
            $this->entityManager->persist($pool);


        }

        if ($tournament->getState() == Tournament::STATE_CREATED){
            $tournament->setState(Tournament::STATE_TEAM_FILLED);
        }

//        dump($_POST);
//        dump($pools);
//        dd($tournament);
        $this->entityManager->flush();

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()]) . '#teams'); // Redirect to Tournament's edit page
        exit();
    }


    /**
     * @param string $id Tournament ID
     * Ajax request to delete a team "/ajax/admin/team/delete"
     */
    public function ajaxTeamDelete(string $id)
    {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        $error = false;
        $poolID = $_POST['pool_id'];
        $teamID = $_POST['team_id'];

        if ($pool = $tournament->getPoolID($poolID)) { // If this Pool exists in this tournament
            if ($team = $pool->getTeamID($teamID)) { // If this Team exists in this pool
                $this->entityManager->remove($team);
                $this->entityManager->flush();
            }else {
                $error = true;
            }
        }else {
            $error = true;
        }

        $data = [
            'error' => $error,
        ];

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * @param string $id Tournament ID
     * Ajax request to delete an entire pool "/ajax/admin/pool/delete"
     */
    public function ajaxPoolDelete(string $id)
    {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        preg_match('/(\d+)/', $_POST['pool_id'], $matches);
        $poolID = $matches[1];

        if ($pool = $tournament->getPoolID($poolID)) { // If this Pool exists in this tournament
            foreach ($pool->getTeams() as $team){ // Select all related teams
                $this->entityManager->remove($team); // Delete them
            }
            $this->entityManager->remove($pool);
            $this->entityManager->flush();
        }

        $data = [
            'Done'
        ];

        header('Content-Type: application/json');
        echo json_encode($data);
    }



}