<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;


/**
 * @Entity @Table(name="tournament")
 **/
class Tournament {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;


    /**
     * @var \DateInterval
     * @Column(type="dateinterval")
     */
    protected $date;

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
     * @ORM\OneToMany(targetEntity="Match", mappedBy="tournament")
     */
    protected $matchs;

    /**
     * @var Team[]
     * @ORM\OneToMany(targetEntity="Team", mappedBy="tournament")
     */
    protected $teams;

    /**
     * @var Pool[]
     * @ORM\OneToMany(targetEntity="Pool", mappedBy="tournament")
     */
    protected $pools;

    // TODO Delay time

    /**
     * @return int
     */
    public function getId(): int {
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
     * @return \DateInterval
     */
    public function getDate(): \DateInterval {
        return $this->date;
    }

    /**
     * @param \DateInterval $date
     * @return Tournament
     */
    public function setDate(\DateInterval $date): Tournament {
        $this->date = $date;
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
    public function getTeams(): array {
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

    /**
     * @return Pool[]
     */
    public function getPools(): array {
        return $this->pools;
    }

    /**
     * @param Pool[] $pools
     * @return Tournament
     */
    public function setPools(array $pools): Tournament {
        $this->pools = $pools;
        return $this;
    }

}