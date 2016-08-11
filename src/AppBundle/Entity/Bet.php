<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bet
 *
 * @ORM\Table(name="bet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BetRepository")
 */
class Bet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Score", mappedBy="bet", cascade={"remove"})
     */
    protected $scores;

    /**
     * @var string
     *
     * @ORM\Column(name="fixture", type="string", length=255)
     */
    private $fixture;

    /**
     * @var string
     *
     * @ORM\Column(name="matchday", type="string", length=255)
     */
    private $matchday;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="string", length=11)
     */
    private $status;


    /**
     * @var int
     *
     * @ORM\Column(name="points", type="string", length=11)
     */
    private $points;


    public function __construct()
    {
        $this->status = 0;
        $this->points = 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Bet
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Bet
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set fixture
     *
     * @param string $fixture
     *
     * @return Bet
     */
    public function setFixture($fixture)
    {
        $this->fixture = $fixture;

        return $this;
    }

    /**
     * Get fixture
     *
     * @return string
     */
    public function getFixture()
    {
        return $this->fixture;
    }

    /**
     * Set matchday
     *
     * @param string $matchday
     *
     * @return Bet
     */
    public function setMatchday($matchday)
    {
        $this->matchday = $matchday;

        return $this;
    }

    /**
     * Get matchday
     *
     * @return string
     */
    public function getMatchday()
    {
        return $this->matchday;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Bet
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set points
     *
     * @param string $points
     *
     * @return Bet
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return string
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set sameResults
     *
     * @param string $sameResults
     *
     * @return Bet
     */
    public function setSameResults($sameResults)
    {
        $this->sameResults = $sameResults;

        return $this;
    }

    /**
     * Get sameResults
     *
     * @return string
     */
    public function getSameResults()
    {
        return $this->sameResults;
    }

    /**
     * Set sameScors
     *
     * @param string $sameScors
     *
     * @return Bet
     */
    public function setSameScors($sameScors)
    {
        $this->sameScors = $sameScors;

        return $this;
    }

    /**
     * Get sameScors
     *
     * @return string
     */
    public function getSameScors()
    {
        return $this->sameScors;
    }

    /**
     * Set guessedScors
     *
     * @param string $guessedScors
     *
     * @return Bet
     */
    public function setGuessedScors($guessedScors)
    {
        $this->guessedScors = $guessedScors;

        return $this;
    }

    /**
     * Get guessedScors
     *
     * @return string
     */
    public function getGuessedScors()
    {
        return $this->guessedScors;
    }

    /**
     * Set guessedResults
     *
     * @param string $guessedResults
     *
     * @return Bet
     */
    public function setGuessedResults($guessedResults)
    {
        $this->guessedResults = $guessedResults;

        return $this;
    }

    /**
     * Get guessedResults
     *
     * @return string
     */
    public function getGuessedResults()
    {
        return $this->guessedResults;
    }

    /**
     * Set betResults
     *
     * @param string $betResults
     *
     * @return Bet
     */
    public function setBetResults($betResults)
    {
        $this->betResults = $betResults;

        return $this;
    }

    /**
     * Get betResults
     *
     * @return string
     */
    public function getBetResults()
    {
        return $this->betResults;
    }

    /**
     * Set betScors
     *
     * @param string $betScors
     *
     * @return Bet
     */
    public function setBetScors($betScors)
    {
        $this->betScors = $betScors;

        return $this;
    }

    /**
     * Get betScors
     *
     * @return string
     */
    public function getBetScors()
    {
        return $this->betScors;
    }

    /**
     * Add score
     *
     * @param \AppBundle\Entity\Score $score
     *
     * @return Bet
     */
    public function addScore(\AppBundle\Entity\Score $score)
    {
        $this->scores[] = $score;

        return $this;
    }

    /**
     * Remove score
     *
     * @param \AppBundle\Entity\Score $score
     */
    public function removeScore(\AppBundle\Entity\Score $score)
    {
        $this->scores->removeElement($score);
    }

    /**
     * Get scores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScores()
    {
        return $this->scores;
    }
}
