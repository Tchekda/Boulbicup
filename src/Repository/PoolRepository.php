<?php


namespace Repository;


use Doctrine\ORM\EntityRepository;

class PoolRepository extends EntityRepository {

    public function findByTournament($tournament) {
        return $this->createQueryBuilder('p')
            ->andWhere('p.tournament = :tournament')
            ->setParameter('tournament', $tournament)
            ->getQuery()
            ->getResult();
    }
}