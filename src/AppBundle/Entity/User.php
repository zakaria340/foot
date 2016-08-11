<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser {
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;


  /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Bet", mappedBy="user", cascade={"remove"})
   */
  protected $bets;

  public function __construct() {
    parent::__construct();
    $this->bets = new \Doctrine\Common\Collections\ArrayCollection();
    $this->points = 0;
  }

  /**
   * @var int
   *
   * @ORM\Column(name="points", type="string", length=11)
   */
  private $points;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;


  /**
   * Add bet
   *
   * @param \AppBundle\Entity\Bets $bet
   *
   * @return User
   */
  public function addBet(\AppBundle\Entity\Bet $bet) {
    $this->bets[] = $bet;

    return $this;
  }

  /**
   * Remove bet
   *
   * @param \AppBundle\Entity\Bets $bet
   */
  public function removeBet(\AppBundle\Entity\Bet $bet) {
    $this->bets->removeElement($bet);
  }

  /**
   * Get bets
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getBets() {
    return $this->bets;
  }

  /**
   * Add product
   *
   * @param \AppBundle\Entity\Bets $product
   *
   * @return User
   */
  public function addProduct(\AppBundle\Entity\Bet $product) {
    $this->products[] = $product;

    return $this;
  }

  /**
   * Remove product
   *
   * @param \AppBundle\Entity\Bets $product
   */
  public function removeProduct(\AppBundle\Entity\Bet $product) {
    $this->products->removeElement($product);
  }

  /**
   * Get products
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getProducts() {
    return $this->products;
  }

    /**
     * Set points
     *
     * @param string $points
     *
     * @return User
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
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
