<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


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
     * @Column(type="simple_array")
     * @var int[]
     */
    protected $score;

    /**
     * @var DateTime
     * @Column(type="datetime")
     */
    protected $datetime;


    /**
     * @var bool
     * @Column(type="boolean")
     */
    protected $finished = false;

    public function __construct() {
        $this->score = new ArrayCollection();
    }

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
     * @return string
     */
    public function formatScore(): string {
        return $this->getScore()[0] . ":" . $this->getScore()[1];
    }

    /**
     * @return int[]
     */
    public function getScore(): array {
        return $this->score;
    }

    /**
     * @param int[] $score
     * @return Match
     */
    public function setScore(array $score): Match {
        $this->score = $score;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDatetime(): DateTime {
        return $this->datetime;
    }

    /**
     * @param DateTime $datetime
     * @return Match
     */
    public function setDatetime(DateTime $datetime): Match {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool {
        return $this->finished;
    }

    /**
     * @param bool $finished
     * @return Match
     */
    public function setFinished(bool $finished): Match {
        $this->finished = $finished;
        return $this;
    }



}