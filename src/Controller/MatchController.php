<?php /** @noinspection ALL */


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\Match;
use Entity\User;
use Service\App;
use Service\MatchGenerator;
use DateTime;


require '../vendor/autoload.php';

class MatchController extends BaseController
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
     * MatchController constructor, stores the router instance and the entity manager from the index router.
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


    public function generateMatchs(string $id){
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        $matchGenerator = new MatchGenerator($this->entityManager, $tournament); // Init the MatchGenerator service with Doctrine and the tournament

        if (count($tournament->getMatchs()) != 0){ // If there are some existing matchs
            foreach ($tournament->getMatchs() as $match){ // Remove them
                $this->entityManager->remove($match);
            }
            $this->entityManager->flush();
        }

        $matchs = $matchGenerator->generateTournamentMatchs(); // Generate matchs

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()])); // Redirect to tournament's edit page
        exit();
    }


    public function ajaxMatchEdit(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        if (!empty($_POST)){ // If Post data is not empty
            /** @var Match $match */
            if($match = $this->entityManager->getRepository("Entity\\Match")->find(intval($_POST['match-id']))){
                $match->setHost($this->entityManager->getRepository("Entity\\Team")->find(intval($_POST['host-team'])));
                $match->setAway($this->entityManager->getRepository("Entity\\Team")->find(intval($_POST['away-team'])));
                $match->setHostScore(intval($_POST['host-score']));
                $match->setAwayScore(intval($_POST['away-score']));
                $match->setTime(DateTime::createFromFormat('d/m/Y H:i', $_POST['match-day'] . ' ' . $_POST['match-time']));
                $match->setType(intval($_POST['match-type']));
                $match->setState(intval($_POST['match-state']));
                $this->entityManager->flush();

                $data = [
                    'id' => $match->getId(),
                    'host' => $match->getHost()->getName(),
                    'away' => $match->getAway()->getName(),
                    'score' => $match->getHostScore() . ' : ' . $match->getAwayScore(),
                    'time' => $match->getTime()->format("d/m/Y H:i"),
                    'type' => $match->getTypeName(),
                    'state' => $match->getStateName()
                ];

                header('HTTP/1.0 200 OK');
                header('Content-Type: application/json');
                echo json_encode($data);
            }else {
                header('HTTP/1.0 404 Not Found');
                echo 'Match not found';
                exit();
            }
        }else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Post Data is empty';
            exit();
        }

    }


}
