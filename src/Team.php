<?php


/**
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
     * @ManyToOne(targetEntity="Pool")
     * @var Pool
     */
    protected $pool;
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

}