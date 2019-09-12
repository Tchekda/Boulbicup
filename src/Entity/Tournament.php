<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;


/**
 * @Entity(repositoryClass="Repository\TournamentRepository")
 * @Table(name="tournament")
 **/
class Tournament {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;


    /**
     * @var \DateTime
     * @Column(type="datetime")
     */
    protected $start_datetime;

    /**
     * @var \DateTime
     * @Column(type="datetime")
     */
    protected $end_datetime;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $name;

    /**
     * @var int
     * @Column(type="integer")
     */
    protected $category;

    /**
     * @var Match[]
     * @ORM\OneToMany(targetEntity="Match", mappedBy="tournament", cascade={"remove"})
     */
    protected $matchs;

    /**
     * @var Team[]
     * @ORM\OneToMany(targetEntity="Team", mappedBy="tournament", cascade={"remove"})
     */
    protected $teams;

    /**
     * @var Pool[]
     * @ORM\OneToMany(targetEntity="Pool", mappedBy="tournament", cascade={"remove"})
     */
    protected $pools;

    // TODO Delay time

    public function __construct() {
        $this->matchs = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->pools = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Match
     */
    public function setId(int $id): Tournament {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDatetime(): \DateTime {
        return $this->start_datetime;
    }

    /**
     * @param \DateTime $start_datetime
     * @return Tournament
     */
    public function setStartDatetime(\DateTime $start_datetime): Tournament {
        $this->start_datetime = $start_datetime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDatetime(): \DateTime {
        return $this->end_datetime;
    }

    /**
     * @param \DateTime $end_datetime
     * @return Tournament
     */
    public function setEndDatetime(\DateTime $end_datetime): Tournament {
        $this->end_datetime = $end_datetime;
        return $this;
    }


    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Tournament
     */
    public function setName(string $name): Tournament {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getCategory(): int {
        return $this->category;
    }

    /**
     * @param int $category
     * @return Tournament
     */
    public function setCategory(int $category): Tournament {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Match[]
     */
    public function getMatchs(): array {
        return $this->matchs;
    }

    /**
     * @param Match[] $matchs
     * @return Tournament
     */
    public function setMatchs(array $matchs): Tournament {
        $this->matchs = $matchs;
        return $this;
    }

    /**
     * @return Team[]
     */
    public function getTeams() {
        return $this->teams;
    }

    /**
     * @param Team[] $teams
     * @return Tournament
     */
    public function setTeams(array $teams): Tournament {
        $this->teams = $teams;
        return $this;
    }

    public function addTeam(Team $team): Tournament {
        $this->teams[] = $team;
        return $this;
    }



    /**
     * @return Pool[]
     */
    public function getPools() {
        if (!$this->pools){
            $this->pools = new ArrayCollection();
        }
        return $this->pools;
    }

    /**
     * @param string $name
     * @return Pool|null
     */
    public function getPool(string $name) {
        if (!$this->pools){
            $this->pools = new ArrayCollection();
        }
        foreach ($this->pools as $pool){
            if ($pool->getName() == $name){
                return $pool;
            }
        }
        return null;
    }

    /**
     * @param Pool[] $pools
     * @return Tournament
     */
    public function setPools(array $pools): Tournament {
        $this->pools = $pools;
        return $this;
    }

    public function addPool(Pool $pool): Tournament{
        $this->pools[] = $pool;
        return $this;
    }

}