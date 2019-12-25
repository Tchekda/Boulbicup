<?php


namespace Service;


use Entity\Pool;
use Entity\Team;
use Entity\Tournament;

class Ranking {


    /**
     * @var Tournament
     */
    private $tournament;

    public function __construct(Tournament $tournament) {
        $this->tournament = $tournament;
    }

    /**
     * @return Team[]
     */
    public function getGlobalRanking(){
        $teams = $this->tournament->getTeams()->getValues();
        usort($teams, function ($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        return $teams;
    }

    /**
     * @param Pool $pool
     * @return Team[]
     */
    public function getPoolRanking(Pool $pool){
        $teams = $pool->getTeams()->getValues();
        usort($teams, function ($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        return $teams;
    }

    /**
     * @return array
     */
    public function getAllPoolsRanking(){
        $teams = array();
        foreach ($this->tournament->getPools() as $pool){
            $teams[$pool->getName()] = $this->getPoolRanking($pool);
        }
        return $teams;
    }

    public function getAllMergedRankings(){
        $result = array_merge(array("all" => $this->getGlobalRanking()), $this->getAllPoolsRanking());
        return $result;
    }

    public function getStandardisedData(){
        $result = array();
        $data = $this->getAllMergedRankings();

        foreach ($data as $pool => $teams) {
            $result[$pool] = array();
            /** @var Team $team */
            foreach ($teams as $team) {
                $result[$pool][] = array(
                    'id' => $team->getId(),
                    'name' => $team->getName(),
                    'points' => $team->getPoints(),
                    'pool' => $team->getPool()->getName()
                );
            }
        }
        return $result;
    }
}