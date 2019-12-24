<?php


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\Match;
use Entity\Tournament;
use Entity\User;
use Service\App;
use Service\MatchGenerator;
use DateTime;


require '../vendor/autoload.php';

class MatchController extends BaseController {

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
     * @param string $id Tournament ID
     * (Re)Generate pool matchs for all the teams registered in the tournament
     */
    public function generatePoolMatchs(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        if ($tournament->getState() < Tournament::STATE_POOL_PHASE) {
            if (count($tournament->getMatchs()) != 0) { // If there are some existing matchs
                foreach ($tournament->getMatchs() as $match) { // Remove them
                    $this->entityManager->remove($match);
                }
                $this->entityManager->flush();
            }

            $matchGenerator = new MatchGenerator($this->entityManager, $tournament); // Init the MatchGenerator service with Doctrine and the tournament

            $matchs = $matchGenerator->generateTournamentMatchs(); // Generate matchs
        }

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()])); // Redirect to tournament's edit page
        exit();
    }

    /**
     * @param string $id Tournament ID
     * Edit's Matchs from data received in the modal box
     */
    public function ajaxPoolMatchEdit(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        if (!empty($_POST)) { // If Post data is not empty
            /** @var Match $match */
            if ($match = $this->entityManager->getRepository("Entity\\Match")->find(intval($_POST['match-id']))) {
                if ($tournament->getState() > Tournament::STATE_PRE_RANKING_PHASE and $match->getType() == Match::TYPE_POOL) {
                    header('HTTP/1.0 403 Forbidden');
                    echo 'Pool phase already ended';
                    exit();
                }
                $previousState = $match->getState();
                $match->setHost($this->entityManager->getRepository("Entity\\Team")->find(intval($_POST['host-team'])));
                $match->setAway($this->entityManager->getRepository("Entity\\Team")->find(intval($_POST['away-team'])));
                $match->setHostScore(intval($_POST['host-score']));
                $match->setAwayScore(intval($_POST['away-score']));
                $match->setTime(DateTime::createFromFormat('d/m/Y H:i', $_POST['match-day'] . ' ' . $_POST['match-time']));
                $match->setType(intval($_POST['match-type']));
                $match->setState(intval($_POST['match-state']));

                if ($tournament->getState() < Tournament::STATE_POOL_PHASE) {
                    $tournament->setState(Tournament::STATE_POOL_PHASE);
                }
                if ($match->getState() < Match::STATE_EXPECTED) {
                    $match->setState(Match::STATE_IN_PROGRESS);
                }
                if ($match->getType() == Match::TYPE_POOL) {
                    if ($previousState != $match->getState() && $match->getState() == Match::STATE_FINISHED) { // Game had just finished
                        if ($match->getHostScore() > $match->getAwayScore()) {
                            $match->getHost()->addPoints(3);
                            $match->getAway()->addPoints(1);
                        } elseif ($match->getHostScore() < $match->getAwayScore()) {
                            $match->getHost()->addPoints(1);
                            $match->getAway()->addPoints(3);
                        } else {
                            $match->getHost()->addPoints(2);
                            $match->getAway()->addPoints(2);
                        }
                        $matchGenerator = new MatchGenerator($this->entityManager, $tournament); // Init the MatchGenerator service with Doctrine and the tournament
                        if ($matchGenerator->allPoolGamesFinished()) {
                            $tournament->setState(Tournament::STATE_PRE_RANKING_PHASE);
                        }
                    }
                }


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
            } else {
                header('HTTP/1.0 404 Not Found');
                echo 'Match not found';
                exit();
            }
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Post Data is empty';
            exit();
        }

    }


    public function ajaxRecalcutatePoints(string $id){
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        foreach ($tournament->getTeams() as $team) {
            $team->setPoints(0);
        }

        foreach ($tournament->getMatchs() as $match) {
            if ($match->getState() == Match::STATE_FINISHED) {
                if ($match->getHostScore() > $match->getAwayScore()) {
                    $match->getHost()->addPoints(3);
                    $match->getAway()->addPoints(1);
                } elseif ($match->getHostScore() < $match->getAwayScore()) {
                    $match->getHost()->addPoints(1);
                    $match->getAway()->addPoints(3);
                } else {
                    $match->getHost()->addPoints(2);
                    $match->getAway()->addPoints(2);
                }
            }
        }

        $this->entityManager->flush();

        $ranked_teams = ['all' => array()];

        foreach ($tournament->getPools() as $pool) {
            $ranked_teams[$pool->getName()] = array();
        }

        foreach ($tournament->getTeams() as $team){
            $ranked_teams['all'][] = $team->toArray();
            $ranked_teams[$team->getPool()->getName()][] = $team->toArray();
        }

        usort($ranked_teams['all'], function ($a, $b) {
            return $a['points'] < $b['points'];
        });

        foreach ($tournament->getPools() as $pool) {
            usort($ranked_teams[$pool->getName()], function ($a, $b) {
                return $a['points'] < $b['points'];
            });
        }

        header('HTTP/1.0 200 OK');
        header('Content-Type: application/json');
        echo json_encode($ranked_teams);
    }
    /**
     * @param string $id
     * Replace Team references by real teams
     */
    public function generateRankingMatchs(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        if ($tournament->getState() == Tournament::STATE_PRE_RANKING_PHASE) {

            $matchGenerator = new MatchGenerator($this->entityManager, $tournament); // Init the MatchGenerator service with Doctrine and the tournament

            $matchs = $matchGenerator->generateRankingMatchs(); // Generate matchs
        }

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()])); // Redirect to tournament's edit page
        exit();
    }
}
