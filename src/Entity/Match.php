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
 * @Entity(repositoryClass="Repository\MatchRepository")
 * @Table(name="`match`", uniqueConstraints={
 *        @UniqueConstraint(name="tournament_match_type",
 *            columns={"tournament_id", "host_id", "away_id", "type"})
 *    })
 **/
class Match {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Team")
     * @JoinColumn(name="host_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Team
     */
    protected $host;

    /**
     * @ManyToOne(targetEntity="Team")
     * @JoinColumn(name="away_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Team
     */
    protected $away;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $host_reference = null;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $away_reference = null;

    /**
     * @Column(type="integer",)
     * @var int
     */
    protected $host_score = 0;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $away_score = 0;

    /**
     * @var \DateTime
     * @Column(type="datetime")
     */
    protected $time;

    /**
     * @var int
     * @Column(type="integer")
     */
    protected $type = 0;

    /**
     * @var int
     * @Column(type="integer")
     */
    protected $state = 0;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var Tournament
     * @ManyToOne(targetEntity="Tournament", inversedBy="matchs")
     * @JoinColumn(name="tournament_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tournament;

    const TYPE_POOL = 0;
    const TYPE_RANKING = 1;

    protected $types = [
        Match::TYPE_POOL => "Match de Pool",
        Match::TYPE_RANKING => "Match de Classement"
    ];

    const STATE_EXPECTED = 0;
    const STATE_IN_PROGRESS = 1;
    const STATE_FINISHED = 2;

    protected $states = [
        Match::STATE_EXPECTED => "Prévu",
        Match::STATE_IN_PROGRESS => "En cours",
        Match::STATE_FINISHED => "Terminé"
    ];

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
    public function setId(int $id): Match {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Team| null
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @param Team $host
     * @return Match
     */
    public function setHost(Team $host): Match {
        $this->host = $host;
        return $this;
    }

    /**
     * @return Team| null
     */
    public function getAway() {
        return $this->away;
    }

    /**
     * @param Team $away
     * @return Match
     */
    public function setAway(Team $away): Match {
        $this->away = $away;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHostReference() {
        return $this->host_reference;
    }

    /**
     * @param string|null $host_reference
     * @return Match
     */
    public function setHostReference($host_reference): Match {
        $this->host_reference = $host_reference;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAwayReference() {
        return $this->away_reference;
    }

    /**
     * @param string|null $away_reference
     * @return Match
     */
    public function setAwayReference($away_reference): Match {
        $this->away_reference = $away_reference;
        return $this;
    }

    /**
     * @return int
     */
    public function getHostScore(): int {
        return $this->host_score;
    }

    /**
     * @param int $host_score
     * @return Match
     */
    public function setHostScore(int $host_score): Match {
        $this->host_score = $host_score;
        return $this;
    }

    /**
     * @return int
     */
    public function getAwayScore(): int {
        return $this->away_score;
    }

    /**
     * @param int $away_score
     * @return Match
     */
    public function setAwayScore(int $away_score): Match {
        $this->away_score = $away_score;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     * @return Match
     */
    public function setTime(\DateTime $time): Match {
        $this->time = $time;
        return $this;
    }

    /**
     * @return integer
     */
    public function getType(): int {
        return $this->type;
    }


    /**
     * @return string
     */
    public function getTypeName(): string {
        return $this->types[$this->type];
    }

    /**
     * @return array
     */
    public function getTypes(): array {
        return $this->types;
    }


    /**
     * @param int $type
     * @return Match
     */
    public function setType(int $type): Match {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getState(): int {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getStateName(): string {
        return $this->states[$this->state];
    }

    /**
     * @return boolean
     */
    public function isState(int $state): bool {
        return $state == $this->state;
    }

    /**
     * @return array
     */
    public function getStates(): array {
        return $this->states;
    }

    /**
     * @param int $state
     * @return Match
     */
    public function setState(int $state): Match {
        $this->state = $state;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Match
     */
    public function setName(string $name): Match {
        $this->name = $name;
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
     * @return Match
     */
    public function setTournament(Tournament $tournament): Match {
        $this->tournament = $tournament;
        return $this;
    }


}