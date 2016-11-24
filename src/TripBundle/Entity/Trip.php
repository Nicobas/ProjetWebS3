<?php

namespace TripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Trip
 *
 * @ORM\Table(name="trip")
 * @ORM\Entity(repositoryClass="TripBundle\Repository\TripRepository")
 */
class Trip
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->admins = new \Doctrine\Common\Collections\ArrayCollection();
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setStatut("Configuration");
    }

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
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=32)
     */
    private $statut;

    /**
     * @var bool
     *
     * @ORM\Column(name="ouvert", type="boolean")
     */
    private $ouvert;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime")
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="lieux", type="string")
     */
    private $lieux;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", cascade={"persist"}, mappedBy="administrableTrips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admins;

    /**
     * @ORM\OneToMany(targetEntity="TripBundle\Entity\RestrictionTrip", mappedBy="trip", cascade={"persist", "remove"})
     */
    private $restrictions;

    /**
     * @ORM\OneToMany(targetEntity="TripBundle\Entity\TripParticipant", mappedBy="trip", cascade={"persist", "remove"})
     */
    private $participants;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

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
     * Set titre
     *
     * @param string $titre
     *
     * @return Trip
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return Trip
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set ouvert
     *
     * @param boolean $ouvert
     *
     * @return Trip
     */
    public function setOuvert($ouvert)
    {
        $this->ouvert = $ouvert;

        return $this;
    }

    /**
     * Get ouvert
     *
     * @return bool
     */
    public function getOuvert()
    {
        return $this->ouvert;
    }

    /**
     * Add admin
     *
     * @param \UserBundle\Entity\User $admin
     *
     * @return Trip
     */
    public function addAdmin(\UserBundle\Entity\User $admin)
    {
        $this->admins[] = $admin;
        $admin->addAdministrableTrip($this);

        return $this;
    }

    /**
     * Remove admin
     *
     * @param \UserBundle\Entity\User $admin
     */
    public function removeAdmin(\UserBundle\Entity\User $admin)
    {
        $this->admins->removeElement($admin);
    }

    /**
     * Get admins
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Trip
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Trip
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set lieux
     *
     * @param string $lieux
     *
     * @return Trip
     */
    public function setLieux($lieux)
    {
        $this->lieux = $lieux;

        return $this;
    }

    /**
     * Get lieux
     *
     * @return string
     */
    public function getLieux()
    {
        return $this->lieux;
    }

    /**
     * Add restriction
     *
     * @param \TripBundle\Entity\RestrictionTrip $restriction
     *
     * @return Trip
     */
    public function addRestriction(\TripBundle\Entity\RestrictionTrip $restriction)
    {
        $this->restrictions[] = $restriction;

        return $this;
    }

    /**
     * Remove restriction
     *
     * @param \TripBundle\Entity\RestrictionTrip $restriction
     */
    public function removeRestriction(\TripBundle\Entity\RestrictionTrip $restriction)
    {
        $this->restrictions->removeElement($restriction);
    }

    /**
     * Get restrictions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * Add participant
     *
     * @param \TripBundle\Entity\TripParticipant $participant
     *
     * @return Trip
     */
    public function addParticipant(\TripBundle\Entity\TripParticipant $participant)
    {
        $this->participants[] = $participant;

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \TripBundle\Entity\TripParticipant $participant
     */
    public function removeParticipant(\TripBundle\Entity\TripParticipant $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Trip
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
