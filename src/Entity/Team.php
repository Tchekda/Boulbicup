<?php

namespace Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @Entity(repositoryClass="Repository\TeamRepository")
 * @Table(name="team", uniqueConstraints={
 *     @UniqueConstraint(name="team_tournament",
 *            columns={"name", "tournament_id"}),
 *     @UniqueConstraint(name="team_pool",
 *            columns={"name", "pool_id"})
 *    }))
 **/
class Team
{

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     **/
    protected $name;

    /**
     * @Column(type="integer")
     * @var int
     **/
    protected $points = 0;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $finalRanking = 0;

    /**
     * @ManyToOne(targetEntity="Pool", inversedBy="teams")
     * @JoinColumn(name="pool_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Pool
     */
    protected $pool;

    /**
     * @var Tournament
     * @ManyToOne(targetEntity="Tournament", inversedBy="teams")
     * @JoinColumn(name="tournament_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tournament;


    public function toArray() {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param mixed $points
     */
    public function addPoints($points): void
    {
        $this->points += $points;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points): void
    {
        $this->points = $points;
    }

    /**
     * @return int
     */
    public function getFinalRanking(): int {
        return $this->finalRanking;
    }

    /**
     * @param int $finalRanking
     * @return Team
     */
    public function setFinalRanking(int $finalRanking): Team {
        $this->finalRanking = $finalRanking;
        return $this;
    }


    /**
     * @return Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }

    /**
     * @param Pool $pool
     */
    public function setPool(Pool $pool): void
    {
        $this->pool = $pool;
    }

    /**
     * @return Tournament
     */
    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     * @return Team
     */
    public function setTournament(Tournament $tournament): Team
    {
        $this->tournament = $tournament;
        return $this;
    }

}