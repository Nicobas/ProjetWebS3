<?php

namespace TripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RestrictionTrip
 *
 * @ORM\Table(name="restriction_trip")
 * @ORM\Entity(repositoryClass="TripBundle\Repository\RestrictionTripRepository")
 */
class RestrictionTrip
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
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="inscrit", type="boolean")
     */
    private $inscrit;

    /**
     * @ORM\ManyToOne(targetEntity="TripBundle\Entity\Trip", cascade={"persist"}, inversedBy="restrictions")
     */
    private $trip;


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
     * Set email
     *
     * @param string $email
     *
     * @return RestrictionTrip
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set inscrit
     *
     * @param boolean $inscrit
     *
     * @return RestrictionTrip
     */
    public function setInscrit($inscrit)
    {
        $this->inscrit = $inscrit;

        return $this;
    }

    /**
     * Get inscrit
     *
     * @return bool
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * Set trip
     *
     * @param \TripBundle\Entity\Trip $trip
     *
     * @return RestrictionTrip
     */
    public function setTrip(\TripBundle\Entity\Trip $trip = null)
    {
        $this->trip = $trip;

        return $this;
    }

    /**
     * Get trip
     *
     * @return \TripBundle\Entity\Trip
     */
    public function getTrip()
    {
        return $this->trip;
    }
}
