<?php


namespace Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Tournament;
use Entity\User;
use Service\App;

require '../vendor/autoload.php';

class TournamentController extends BaseController {

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
     * Tournament list page, displays all registered tournament to edit/delete them "/admin/tournaments"
     */
    public function tournamentList()
    {
        /** @var Tournament[] $tournaments */
        $tournaments = $this->entityManager->getRepository("Entity\\Tournament")->findAll(); // Get all tournament registered
        echo $this->twig->render('admin/lists/tournaments.html.twig', ['tournaments' => $tournaments]);
    }

    /**
     * New tournament form request function, "GET:/admin/tournament/new"
     */
    public function tournamentNew()
    {
        echo $this->twig->render('admin/manage/tournament/tournament_new.html.twig');
    }

    /**
     * Tournament submission function (New or Edit), "POST:/admin/tournament/submit"
     */
    public function tournamentSubmitForm()
    {
        if (empty($_POST)) { // If nothing is posted
            header('Location: ' . $this->router->generate('admin_login'));
            exit();
        } elseif (!isset($_POST['name']) || !isset($_POST['category']) || !isset($_POST['date_first'])
            || !isset($_POST['start_time_first']) || !isset($_POST['end_time_first'])) { // If missing one of the required field
            $notifications['red'] = "Vous n'avez pas rempli tous les champs obligatoires"; // Display a notification
            if (isset($_POST['tournament_id'])) { // If tournament is edited
                header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => intval($_POST['tournament_id'])])); // Redirect to edit page
                exit();
            } else {
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
    public function tournamentEdit(string $id)
    {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        echo $this->twig->render('admin/manage/tournament/tournament_edit.html.twig', ['tournament' => $tournament]);
    }

}