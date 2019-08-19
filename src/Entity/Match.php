<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;


/**
 * @Entity @Table(name="match")
 **/
class Match {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Team")
     * @var Team
     */
    protected $host;

    /**
     * @ManyToOne(targetEntity="Team")
     * @var Team
     */
    protected $away;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $host_score = 0;

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $away_score = 0;

    /**
     * @var \DateInterval
     * @Column(type="dateinterval")
     */
    protected $time;

    /**
     * @var Tournament
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="matchs")
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
     * @return \DateInterval
     */
    public function getTime(): \DateInterval {
        return $this->time;
    }

    /**
     * @param \DateInterval $time
     * @return Match
     */
    public function setTime(\DateInterval $time): Match {
        $this->time = $time;
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