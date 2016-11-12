<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    protected $prenom;

    /**
     * @ORM\Column(name="nom", type="string", length=255)
     */
    protected $nom;

    /**
     * @ORM\Column(name="promotion", type="string", length=255)
     */
    protected $promotion;

    /**
     * @ORM\ManyToMany(targetEntity="TripBundle\Entity\Trip", cascade={"persist"}, inversedBy="admins")
     * @ORM\JoinTable(name="trip_user")
     */
    protected $administrableTrips;

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set promotion
     *
     * @param string $promotion
     *
     * @return User
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return string
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    /**
     * Add administrableTrip
     *
     * @param \TripBundle\Entity\Trip $administrableTrip
     *
     * @return User
     */
    public function addAdministrableTrip(\TripBundle\Entity\Trip $administrableTrip)
    {
        $this->administrableTrips[] = $administrableTrip;

        return $this;
    }

    /**
     * Remove administrableTrip
     *
     * @param \TripBundle\Entity\Trip $administrableTrip
     */
    public function removeAdministrableTrip(\TripBundle\Entity\Trip $administrableTrip)
    {
        $this->administrableTrips->removeElement($administrableTrip);
    }

    /**
     * Get administrableTrips
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdministrableTrips()
    {
        return $this->administrableTrips;
    }
}
