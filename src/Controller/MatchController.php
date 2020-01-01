<?php


namespace Controller;

use Doctrine\ORM\EntityManagerInterface;
use Entity\Match;
use Entity\Tournament;
use Entity\User;
use Service\App;
use Service\MatchGenerator;
use DateTime;
use Service\Ranking;


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
            }
            foreach ($tournament->getTeams() as $team) {
                $team->setPoints(0);
            }
            $this->entityManager->flush();


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

                $matchs = array();
                $matchGenerator = new MatchGenerator($this->entityManager, $tournament);
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
                if ($match->getState() <= Match::STATE_EXPECTED) {
                    $match->setState(Match::STATE_IN_PROGRESS);
                    foreach ($tournament->getMatchs() as $tournament_matchs){
                        if ($tournament_matchs->getState() == Match::STATE_IN_PROGRESS and $tournament_matchs->getId() != $match->getId()){
                            if ($tournament->getState() <= Tournament::STATE_PRE_RANKING_PHASE){
                                $tournament_matchs->setState(Match::STATE_FINISHED);
                            }else {
                                header('HTTP/1.0 403 Forbidden');
                                echo "You need to finish the previous game";
                                exit();
                            }
                        }
                    }
                }
                if ($previousState != $match->getState() && $match->getState() == Match::STATE_FINISHED) { // Game had just finished
                    if ($match->getType() == Match::TYPE_POOL) {
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
                        if ($matchGenerator->allPoolGamesFinished()) {
                            $tournament->setState(Tournament::STATE_PRE_RANKING_PHASE);
                        }
                    } else { // Ranking Game
                        if ($match->getHostScore() > $match->getAwayScore()) {
                            $winner = $match->getHost();
                            $looser = $match->getAway();
                        } elseif ($match->getHostScore() < $match->getAwayScore()) {
                            $looser = $match->getHost();
                            $winner = $match->getAway();
                        } else { // Equal score not possible
                            header('HTTP/1.0 403 Forbidden');
                            echo "Can't end with an equal score";
                            exit();
                        }
                        if ($match->getName() != null) { // Never knows....
                            if (preg_match("/^PO\d$/", $match->getName())) { // PO's Game
                                header('HTTP/1.0 403 Forbidden');
                                /** @var Match $winnerGame Get winner's next game */
                                $winnerGame = $this->entityManager->getRepository("Entity\\Match")->findFutureRankingMatch("V", $match->getName());
                                /** @var Match $looserGame */
                                $looserGame = $this->entityManager->getRepository("Entity\\Match")->findFutureRankingMatch("L", $match->getName());


                                if ($winnerGame->getHostReference() != null and $winnerGame->getHostReference() == "V(" . $match->getName() . ")") {
                                    $winnerGame->setHost($winner);
                                    $winnerGame->setHostReference(null);
                                } else {
                                    $winnerGame->setAway($winner);
                                    $winnerGame->setAwayReference(null);
                                }

                                if ($looserGame->getHostReference() != null and $looserGame->getHostReference() == "L(" . $match->getName() . ")") {
                                    $looserGame->setHost($looser);
                                    $looserGame->setHostReference(null);
                                } else {
                                    $looserGame->setAway($looser);
                                    $looserGame->setAwayReference(null);
                                }

                                $matchs[] = MatchGenerator::standardiseMatchData($winnerGame);
                                $matchs[] = MatchGenerator::standardiseMatchData($looserGame);
                            }else { // Final ranking
                                preg_match("/^(\d+)-(\d+)$/", $match->getName(), $finalRanks);
                                $winner->setFinalRanking(intval($finalRanks[1]));
                                $looser->setFinalRanking(intval($finalRanks[2]));

                                if ($matchGenerator->allGamesFinished()){
                                    $tournament->setState(Tournament::STATE_FINISHED);
                                }
                            }
                        }else { // Game without name
                            header('HTTP/1.0 403 Forbidden');
                            echo "This game has no name";
                            exit();
                        }

                    }
                }


                $this->entityManager->flush();


                $matchs[] = MatchGenerator::standardiseMatchData($match);

                header('HTTP/1.0 200 OK');
                header('Content-Type: application/json');
                echo json_encode($matchs);
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


    public function ajaxRecalcutatePoints(string $id) {
        $tournament = $this->findTournamentByID($id); // Try to find the tournament by the given id, If not found : redirected to tournaments list

        $matchGenerator = new MatchGenerator($this->entityManager, $tournament);
        $matchGenerator->recalculatePoints();

        $ranking = new Ranking($tournament);

        $ranked_teams = $ranking->getStandardisedData();


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

            $matchGenerator = new MatchGenerator($this->entityManager, $tournament);
            $matchGenerator->recalculatePoints();

            $matchGenerator = new MatchGenerator($this->entityManager, $tournament); // Init the MatchGenerator service with Doctrine and the tournament

            $matchGenerator->generateRankingMatchs(); // Generate matchs

            $tournament->setState(Tournament::STATE_RANKING_PHASE);

            $this->entityManager->flush();
        }

        header('Location: ' . $this->router->generate('admin_tournament_edit', ['id' => $tournament->getId()])); // Redirect to tournament's edit page
        exit();
    }
}
