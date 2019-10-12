<?php

namespace Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;


/**
 * @Entity(repositoryClass="Repository\MatchRepository")
 * @Table(name="match", uniqueConstraints={
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
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $type = null;

    /**
     * @var Tournament
     * @ManyToOne(targetEntity="Tournament", inversedBy="matchs")
     * @JoinColumn(name="tournament_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tournament;

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
     * @return Team
     */
    public function getHost(): Team {
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
     * @return Team
     */
    public function getAway(): Team {
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
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Match
     */
    public function setType(string $type): Match {
        $this->type = $type;
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