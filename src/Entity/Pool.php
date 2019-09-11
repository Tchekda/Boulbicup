<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="Repository\PoolRepository")
 * @Table(name="pool")
 **/
class Pool {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @var Team[]
     * @ORM\OneToMany(targetEntity="Team", mappedBy="pool")
     */
    protected $teams;

    /**
     * @var Tournament
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="pools")
     * @ORM\JoinColumn(name="tournament", referencedColumnName="id")
     */
    protected $tournament;

    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }

    /**
     * @return Team[]
     */
    public function getTeams(): array {
        return $this->teams;
    }

    /**
     * @param Team[] $teams
     * @return Pool
     */
    public function setTeams(array $teams): Pool {
        $this->teams = $teams;
        return $this;
    }

    /**
     * @return Tournament
     */
    public function getTournament(): Tournament {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     * @return Pool
     */
    public function setTournament(Tournament $tournament): Pool {
        $this->tournament = $tournament;
        return $this;
    }

}