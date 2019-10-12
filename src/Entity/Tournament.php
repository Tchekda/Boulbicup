<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
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
    protected $start_datetime_first_day;

    /**
     * @var \DateTime
     * @Column(type="datetime")
     */
    protected $end_datetime_first_day;

    /**
     * @var \DateTime
     * @Column(type="datetime", nullable=true)
     */
    protected $start_datetime_second_day;

    /**
     * @var \DateTime
     * @Column(type="datetime", nullable=true)
     */
    protected $end_datetime_second_day;

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
     * @OneToMany(targetEntity="Match", mappedBy="tournament", cascade={"persist", "remove"})
     */
    protected $matchs;

    /**
     * @var Team[]
     * @OneToMany(targetEntity="Team", mappedBy="tournament", cascade={"persist", "remove"})
     */
    protected $teams;

    /**
     * @var Pool[]
     * @OneToMany(targetEntity="Pool", mappedBy="tournament", cascade={"persist", "remove"})
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
    public function getStartDatetimeFirstday(): \DateTime {
        return $this->start_datetime_first_day;
    }

    /**
     * @param \DateTime $start_datetime_first_day
     * @return Tournament
     */
    public function setStartDatetimeFirstday(\DateTime $start_datetime_first_day): Tournament {
        $this->start_datetime_first_day = $start_datetime_first_day;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDatetimeFirstday(): \DateTime {
        return $this->end_datetime_first_day;
    }

    /**
     * @param \DateTime $end_datetime_first_day
     * @return Tournament
     */
    public function setEndDatetimeFirstday(\DateTime $end_datetime_first_day): Tournament {
        $this->end_datetime_first_day = $end_datetime_first_day;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDatetimeSecondDay() {
        return $this->start_datetime_second_day;
    }

    /**
     * @param \DateTime $start_datetime_second_day
     * @return Tournament
     */
    public function setStartDatetimeSecondDay(\DateTime $start_datetime_second_day): Tournament {
        $this->start_datetime_second_day = $start_datetime_second_day;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDatetimeSecondDay() {
        return $this->end_datetime_second_day;
    }

    /**
     * @param \DateTime $end_datetime_second_day
     * @return Tournament
     */
    public function setEndDatetimeSecondDay(\DateTime $end_datetime_second_day): Tournament {
        $this->end_datetime_second_day = $end_datetime_second_day;
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
    public function getMatchs() {
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
        return $this->pools;
    }

    /**
     * @param string $name
     * @return Pool|null
     */
    public function getPool(string $name) {
        foreach ($this->pools as $pool){
            if ($pool->getName() == $name){
                return $pool;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @return Pool|null
     */
    public function getPoolID(int $id) {
        foreach ($this->pools as $pool){
            if ($pool->getId() == $id){
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