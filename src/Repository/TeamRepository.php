<?php


namespace Repository;


use Doctrine\ORM\EntityRepository;

class TeamRepository extends EntityRepository {

//    public function findByTournament($id) {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.tournament = :tournament')
//            ->setParameter('tournament', $id)
//            ->getQuery()
//            ->getResult();
//    }
}