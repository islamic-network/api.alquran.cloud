<?php
// entity/Sajda.php

namespace Api\Entities\Doctrine\Primary;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="sajda")
 **/
class Sajda
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     **/
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Ayat", mappedBy="sajda")
     **/
    protected $ayat;

    /**
     * @ORM\Column(type="boolean")
     **/
    protected $recommended;

    /**
     * @ORM\Column(type="boolean")
     **/
    protected $obligatory;

    /**
     * Constructor
     */
    public function __construct()
    {

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
     * Get recommended
     *
     * @return boolean
     */
    public function getRecommended()
    {
        return $this->recommended;
    }

    /**
     * Get obligatory
     *
     * @return boolean
     */
    public function getObligatory()
    {
        return $this->obligatory;
    }

    /**
     * Set ayat
     *
     * @param Ayat $ayat
     *
     * @return Sajda
     */
    public function setAyat(Ayat $ayat)
    {
        $this->ayat = $ayat;

        return $this;
    }

    /**
     * Get ayat
     *
     * @return Ayat
     */
    public function getAyat()
    {
        return $this->ayat;
    }

    public function get()
    {
        return $this;
    }
}
