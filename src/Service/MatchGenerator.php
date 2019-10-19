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


        $matchsEntities = array();
        $previousGameTime = $this->tournament->getStartDatetimeFirstday();

        foreach ($sortedMatches as $match) {
            $matchEntity = new Match();
            $matchEntity->setHost($match[0]);
            $matchEntity->setAway($match[1]);
            $matchEntity->setTime($previousGameTime);
            $matchEntity->setTournament($this->tournament);
            $this->entityManager->persist($matchEntity);
            array_push($matchsEntities, $matchEntity);
            $previousGameTime->add(new DateInterval("PT" . $this->tournament->getGameTime() . "M"));
            if ($this->tournament->getEndDatetimeFirstday() != null) {
                if ($previousGameTime->format('Hi') > $this->tournament->getEndDatetimeFirstday()->format('Hi')) {
                    $previousGameTime->setTime(
                        intval($this->tournament->getStartDatetimeSecondDay()->format('H')),
                        intval($this->tournament->getStartDatetimeSecondDay()->format('i'))
                    );
                    date_add($previousGameTime, date_interval_create_from_date_string('1 days'));
                }
            }
            if ($i % $this->tournament->getIceRefectionFrequence() == 0 and $i != 0)
                $previousGameTime->add(new DateInterval("PT20M")); //Mounir

        }

        $this->entityManager->flush();

        return $matchsEntities;
    }

}