<?php


namespace Repository;


use Doctrine\ORM\EntityRepository;

class TournamentRepository extends EntityRepository {

    public function findFutureTournaments(){
        return $this->createQueryBuilder('t')
            ->andWhere('t.start_datetime_first_day > CURRENT_DATE()')
            ->orderBy('t.start_datetime_first_day', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAll(bool $reverse = false) {
        return $this->createQueryBuilder('t')
            ->orderBy('t.start_datetime_first_day', ($reverse ? 'ASC' : 'DESC'))
            ->getQuery()
            ->getResult();
    }

    public function findByID(int $id){
        return $this->createQueryBuilder('t')
            ->leftJoin('t.pools', 'p')
            //->leftJoin('t.matchs', 'm')
            ->leftJoin('t.teams', 'te')
            ->select('t', 'p', 'te')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}