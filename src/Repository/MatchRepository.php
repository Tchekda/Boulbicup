<?php

namespace Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MatchRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MatchRepository extends EntityRepository
{
    public function findAllMatchs() {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->getQuery()
            ->getResult();
    }

    public function findFutureRankingMatch(string $outcome, string $gameID) {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->where("m.host_reference = ?1 OR m.away_reference = ?1")
            ->setParameter(1, $outcome . "(" . $gameID . ")")
            ->getQuery()
            ->getOneOrNullResult();
    }

}
