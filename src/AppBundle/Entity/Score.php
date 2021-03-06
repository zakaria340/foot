<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bet
 *
 * @ORM\Table(name="score")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BetRepository")
 */
class Score
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
   * @var int
   *
   * @ORM\Column(name="scoreAway", type="string", length=11)
   */
  private $scoreHome;


  /**
   * @var int
   *
   * @ORM\Column(name="scoreHome", type="string", length=11)
   */
  private $scoreAway;


  /**
   * @var string
   *
   * @ORM\Column(name="predict", type="string", length=255)
   */
  private $predict;

  /**
   * @var int
   *
   * @ORM\Column(name="status", type="string", length=11)
   */
  private $status;


  /**
   * @var string
   *
   * @ORM\Column(name="resultMatch", type="string", length=255)
   */
  private $resultMatch;

  /**
   * @var int
   *
   * @ORM\Column(name="idfootball", type="string", length=11)
   */
  private $idfootball;

  /**
   * @var bet
   *
   * @ORM\ManyToOne(targetEntity="Bet")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="bet", referencedColumnName="id")
   * })
   */
  private $bet;


  /**
   * @var int
   *
   * @ORM\Column(name="betResults", type="string", length=11, nullable= TRUE)
   */
  private $betResults;

  /**
   * @var int
   *
   * @ORM\Column(name="betScors", type="string", length=11, nullable= TRUE)
   */
  private $betScors;

  /**
   * @var int
   *
   * @ORM\Column(name="guessedScors", type="string", length=11, nullable= TRUE)
   */
  private $guessedScors;

  /**
   * @var int
   *
   * @ORM\Column(name="guessedResults", type="string", length=11, nullable= TRUE)
   */
  private $guessedResults;

  
  
  public function __construct()
  {
    $this->predict = '';
    $this->status = 0;
  }


  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set scoreHome
   *
   * @param string $scoreHome
   *
   * @return Score
   */
  public function setScoreHome($scoreHome)
  {
    $this->scoreHome = $scoreHome;

    return $this;
  }

  /**
   * Get scoreHome
   *
   * @return string
   */
  public function getScoreHome()
  {
    return $this->scoreHome;
  }

  /**
   * Set scoreAway
   *
   * @param string $scoreAway
   *
   * @return Score
   */
  public function setScoreAway($scoreAway)
  {
    $this->scoreAway = $scoreAway;

    return $this;
  }

  /**
   * Get scoreAway
   *
   * @return string
   */
  public function getScoreAway()
  {
    return $this->scoreAway;
  }

  /**
   * Set predict
   *
   * @param string $predict
   *
   * @return Score
   */
  public function setPredict($predict)
  {
    $this->predict = $predict;

    return $this;
  }

  /**
   * Get predict
   *
   * @return string
   */
  public function getPredict()
  {
    return $this->predict;
  }

  /**
   * Set status
   *
   * @param string $status
   *
   * @return Score
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
     * Set bet
     *
     * @param \AppBundle\Entity\Bet $bet
     *
     * @return Score
     */
    public function setBet(\AppBundle\Entity\Bet $bet = null)
    {
        $this->bet = $bet;

        return $this;
    }

    /**
     * Get bet
     *
     * @return \AppBundle\Entity\Bet
     */
    public function getBet()
    {
        return $this->bet;
    }

    /**
     * Set idfootball
     *
     * @param string $idfootball
     *
     * @return Score
     */
    public function setIdfootball($idfootball)
    {
        $this->idfootball = $idfootball;

        return $this;
    }

    /**
     * Get idfootball
     *
     * @return string
     */
    public function getIdfootball()
    {
        return $this->idfootball;
    }

    /**
     * Set betResults
     *
     * @param string $betResults
     *
     * @return Score
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
     * @return Score
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
     * Set guessedScors
     *
     * @param string $guessedScors
     *
     * @return Score
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
     * @return Score
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
     * Set resultMatch
     *
     * @param string $resultMatch
     *
     * @return Score
     */
    public function setResultMatch($resultMatch)
    {
        $this->resultMatch = $resultMatch;

        return $this;
    }

    /**
     * Get resultMatch
     *
     * @return string
     */
    public function getResultMatch()
    {
        return $this->resultMatch;
    }
}
