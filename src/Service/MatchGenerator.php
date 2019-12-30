<?php


namespace Service;


use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Match;
use Entity\Tournament;

class MatchGenerator {


    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Tournament
     */
    private $tournament;

    public function __construct(EntityManagerInterface $entityManager, Tournament $tournament) {
        $this->entityManager = $entityManager;
        $this->tournament = $tournament;
    }

    /**
     * @return array
     * Generates all matchs related to the tournament
     * This algorithm is pretty difficult : I don't event remember how I've done it....
     */
    public function generateTournamentMatchs() {
        $matches = $this->generatePoolMatchs();

        if (count($this->tournament->getPools()) > 1){
            foreach ($this->generateRankingReferenceMatchs() as $match) {
                $matches[] = $match;
            }
        }


        $matchsEntities = array();
        $previousGameTime = $this->tournament->getStartDatetimeFirstday();
        $i = 1;
        foreach ($matches as $match) {
            $matchEntity = new Match();
            if (count($match) == 3) { // reference game
                $matchEntity->setHostReference($match[0]);
                $matchEntity->setAwayReference($match[1]);
                $matchEntity->setName($match[2]);
                $matchEntity->setType(Match::TYPE_RANKING);
            } else {
                $matchEntity->setHost($match[0]);
                $matchEntity->setAway($match[1]);
            }
            $matchEntity->setTime(clone $previousGameTime);
            $matchEntity->setTournament($this->tournament);
            $this->entityManager->persist($matchEntity);
            array_push($matchsEntities, $matchEntity);
            $previousGameTime->add(
                new DateInterval("PT" . (
                        $this->tournament->getWarmupTime() +
                        $this->tournament->getGameTime() +
                        $this->tournament->getPostgameTime()
                    )
                    . "M")
            );
            if ($this->tournament->getEndDatetimeFirstday() != null) {
                if ($previousGameTime > $this->tournament->getEndDatetimeFirstday() and $previousGameTime < $this->tournament->getStartDatetimeSecondDay()) {
                    $previousGameTime->setTime(
                        intval($this->tournament->getStartDatetimeSecondDay()->format('H')),
                        intval($this->tournament->getStartDatetimeSecondDay()->format('i'))
                    );
                    date_add($previousGameTime, date_interval_create_from_date_string('1 days'));
                    $i = 0;
                }
            }
            if (($i % $this->tournament->getIceRefectionFrequence()) == 0 and $i != 0) {
                $previousGameTime->add(new DateInterval("PT15M"));
                echo "Surfacage";
            }
            $i++;
        }
        if ($this->tournament->getState() < Tournament::STATE_GAME_GENERATED) {
            $this->tournament->setState(Tournament::STATE_GAME_GENERATED);
        }
        $this->entityManager->flush();

        return $matchsEntities;
    }

    /**
     * @return array
     * Generates all ranking matchs with game references
     * Comments will suppose that there is 5 teams per pool (A and B)
     */
    private function generateRankingReferenceMatchs() { // TODO: More than 2 Pools and 5 teams per pool
        $matchs = array();
        if (count($this->tournament->getPools()) == 2) {
            if (count($this->tournament->getTeams()) == 10) { // When 5 teams per pool
                $matchs[] = array( // 2nd A vs 3rd B
                    "2:" . $this->tournament->getPools()[0]->getId(),
                    "3:" . $this->tournament->getPools()[1]->getId(),
                    "PO1"
                );

                $matchs[] = array( // 2nd B vs 3rd A
                    "2:" . $this->tournament->getPools()[1]->getId(),
                    "3:" . $this->tournament->getPools()[0]->getId(),
                    "PO2"
                );

                $matchs[] = array( // 4th A vs 5th B
                    "4:" . $this->tournament->getPools()[0]->getId(),
                    "5:" . $this->tournament->getPools()[1]->getId(),
                    "PO3"
                );

                $matchs[] = array( // 4th B vs 5th A
                    "4:" . $this->tournament->getPools()[1]->getId(),
                    "5:" . $this->tournament->getPools()[0]->getId(),
                    "PO4"
                );

                $matchs[] = array( // 1st B vs PO1 : Semi-Final
                    "1:" . $this->tournament->getPools()[1]->getId(),
                    "V(PO1)",
                    "PO5"
                );

                $matchs[] = array( // 1st A vs PO2 : Semi-Final
                    "1:" . $this->tournament->getPools()[0]->getId(),
                    "V(PO2)",
                    "PO6"
                );

                $matchs[] = array( // Winner PO3 vs Winner PO4
                    "V(PO3)",
                    "V(PO4)",
                    "7-8"
                );

                $matchs[] = array( // Looser PO3 vs Looser PO4
                    "L(PO3)",
                    "L(PO4)",
                    "9-10"
                );

                $matchs[] = array( // Looser PO1 vs Looser PO2
                    "L(PO1)",
                    "L(PO2)",
                    "5-6"
                );

                $matchs[] = array( // Looser PO5 vs Looser PO6 : Little Final
                    "L(PO5)",
                    "L(PO6)",
                    "3-4"
                );

                $matchs[] = array( // Winner P05 vs Winner PO6 : Final
                    "V(PO5)",
                    "V(PO6)",
                    "1-2"
                );
            }elseif (count($this->tournament->getTeams()) == 8) { // When 4 teams per pool

                $matchs[] = array(
                    "3:" . $this->tournament->getPools()[0]->getId(),
                    "4:" . $this->tournament->getPools()[1]->getId(),
                    "PO1"
                );

                $matchs[] = array(
                    "4:" . $this->tournament->getPools()[0]->getId(),
                    "3:" . $this->tournament->getPools()[1]->getId(),
                    "PO2"
                );

                $matchs[] = array(
                    "1:" . $this->tournament->getPools()[0]->getId(),
                    "2:" . $this->tournament->getPools()[1]->getId(),
                    "PO3"
                );

                $matchs[] = array(
                    "2:" . $this->tournament->getPools()[0]->getId(),
                    "1:" . $this->tournament->getPools()[1]->getId(),
                    "PO4"
                );


                $matchs[] = array(
                    "L(PO1)",
                    "L(PO2)",
                    "7-8"
                );

                $matchs[] = array(
                    "V(PO1)",
                    "V(PO2)",
                    "5-6"
                );

                $matchs[] = array(
                    "L(PO3)",
                    "L(PO4)",
                    "3-4"
                );

                $matchs[] = array(
                    "V(PO3)",
                    "V(PO4)",
                    "1-2"
                );

            }

        }
        return $matchs;
    }

    /**
     * @return array
     */
    private function generatePoolMatchs(): array {
        $matchs = array();
        foreach ($this->tournament->getPools() as $pool) {
            $poolMatchs = array();
            $teamList = $pool->getTeams()->getValues();
            if (count($teamList) % 2 == 0) { // Even
                for ($i = 0; $i < count($teamList); $i += 2) {
                    if (isset($teamList[$i + 1])) {
                        $poolMatchs[] = [$teamList[$i], $teamList[$i + 1]];
                    }
                }

                $first = array_shift($teamList);
                array_push($teamList, $first);

                for ($i = 0; $i < count($teamList); $i += 2) {
                    if (isset($teamList[$i + 1])) {
                        $poolMatchs[] = [$teamList[$i], $teamList[$i + 1]];
                    }
                }

                array_unshift($teamList, array_pop($teamList));

                for ($index = 2; $index <= round((count($teamList) / 2) + ((count($teamList) - 4) / 2)); $index++) {
                    for ($i = 0; $i < count($teamList); $i++) {
                        if (isset($teamList[$i + $index])) {
                            $poolMatchs[] = [$teamList[$i], $teamList[$i + $index]];
                        }
                    }
                }

            } else { // Odd
                for ($i = 0; $i < count($teamList); $i += 2) {
                    if (isset($teamList[$i + 1])) {
                        $poolMatchs[] = [$teamList[$i], $teamList[$i + 1]];
                    }
                }
                array_unshift($teamList, end($teamList)); // Add the last to the beginning

                for ($i = 0; $i < count($teamList); $i += 2) {
                    if (isset($teamList[$i + 1])) {
                        $poolMatchs[] = [$teamList[$i], $teamList[$i + 1]];
                    }
                }

                array_shift($teamList); // Remove the first (Copied from last position)

                for ($index = 2; $index <= round((count($teamList) / 2) + ((count($teamList) - 5) / 2)); $index++) {
                    for ($i = 0; $i < count($teamList); $i++) {
                        if (isset($teamList[$i + $index])) {
                            $poolMatchs[] = [$teamList[$i], $teamList[$i + $index]];
                        }
                    }
                }

            }
            array_push($matchs, $poolMatchs);
        }

        $sortedMatches = array();

        switch (count($matchs)) {
            case 1:
            default:
                $sortedMatches = $matchs;
                break;
            case 2:
                array_map(function ($a, $b) use (&$sortedMatches) {
                    array_push($sortedMatches, $a, $b);
                }, $matchs[0], $matchs[1]);
                break;
            case 3:
                array_map(function ($a, $b, $c) use (&$sortedMatches) {
                    array_push($sortedMatches, $a, $b, $c);
                }, $matchs[0], $matchs[1], $matchs[2]);
                break;
            case 4:
                array_map(function ($a, $b, $c, $d) use (&$sortedMatches) {
                    array_push($sortedMatches, $a, $b, $c, $d);
                }, $matchs[0], $matchs[1], $matchs[2], $matchs[3]);
                break;
        }
        return $sortedMatches;
    }

    /**
     * @return bool
     * Checks if all Pool games are finished to init the ranking phase
     */
    public function allPoolGamesFinished(): bool {
        foreach ($this->tournament->getMatchs() as $match) {
            if ($match->getType() == Match::TYPE_POOL and $match->getState() != Match::STATE_FINISHED) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     * Checks if all Pool games are finished to init the ranking phase
     */
    public function allGamesFinished(): bool {
        foreach ($this->tournament->getMatchs() as $match) {
            if ($match->getState() != Match::STATE_FINISHED) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recalculates all teams points in this tournament
     */
    public function recalculatePoints() {
        foreach ($this->tournament->getTeams() as $team) {
            $team->setPoints(0);
        }

        foreach ($this->tournament->getMatchs() as $match) {
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
    }

    /**
     * Generates Ranking matchs after the pool phase
     */
    public function generateRankingMatchs() {
        $rankings = array();
        $ranking = new Ranking($this->tournament);
        foreach ($this->tournament->getPools() as $pool) {
            $rankings[$pool->getId()] = $ranking->getPoolRanking($pool);
        }


        foreach ($this->tournament->getMatchs() as $match) {
            if ($match->getType() == Match::TYPE_RANKING) {
                if (preg_match('/(\d):(\d)/', $match->getHostReference(), $host_data)) {
                    $match->setHost($rankings[intval($host_data[2])][intval($host_data[1] - 1)]);
                    $match->setHostReference(null);
                }
                if (preg_match('/(\d):(\d)/', $match->getAwayReference(), $away_data)) {
                    $match->setAway($rankings[intval($away_data[2])][intval($away_data[1] - 1)]);
                    $match->setAwayReference(null);
                }
            }
        }
        $this->entityManager->flush();
        return $this->tournament->getMatchs();
    }


    /**
     * @param Match $match
     * @return array
     */
    public static function standardiseMatchData(Match $match) {
        $data = [
            'id' => $match->getId(),
            'host' => $match->getHost() ? $match->getHost()->getName() : $match->getHostReference(),
            'away' => $match->getAway() ? $match->getAway()->getName() : $match->getAwayReference(),
            'score' => $match->getHostScore() . ' : ' . $match->getAwayScore(),
            'time' => $match->getTime()->format("d/m/Y H:i"),
            'type' => $match->getTypeName(),
            'state' => $match->getStateName()
        ];
        return $data;
    }
}