<?php

namespace Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @Entity(repositoryClass="Repository\TeamRepository")
 * @Entity @Table(name="team")
 **/
class Team {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string", unique=true)
     * @var string
     **/
    protected $name;

    /**
     * @Column(type="integer")
     * @var int
     **/
    protected $points;

    /**
     * @ORM\ManyToOne(targetEntity="Pool", inversedBy="teams")
     * @var Pool
     */
    protected $pool;

    /**
     * @var Tournament[]
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="teams")
     */
    protected $tournament;
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
     * @return mixed
     */
    public function getPoints() {
        return $this->points;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points): void {
        $this->points = $points;
    }

    /**
     * @return Pool
     */
    public function getPool(): Pool {
        return $this->pool;
    }

    /**
     * @param Pool $pool
     */
    public function setPool(Pool $pool): void {
        $this->pool = $pool;
    }

    /**
     * @return Tournament[]
     */
    public function getTournament(): array {
        return $this->tournament;
    }

    /**
     * @param Tournament[] $tournament
     * @return Team
     */
    public function setTournament(array $tournament): Team {
        $this->tournament = $tournament;
        return $this;
    }

}