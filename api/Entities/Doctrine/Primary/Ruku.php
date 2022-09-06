<?php
// entity/Ruku.php

namespace Api\Entities\Doctrine\Primary;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="ruku")
 **/
class Ruku
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     **/
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Ayat", mappedBy="ruku")
     **/
    protected $ayats;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ayats = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add ayat
     *
     * @param Ayat $ayat
     *
     * @return Ruku
     */
    public function addAyat(Ayat $ayat)
    {
        $this->ayats[] = $ayat;

        return $this;
    }

    /**
     * Remove ayat
     *
     * @param Ayat $ayat
     */
    public function removeAyat(Ayat $ayat)
    {
        $this->ayats->removeElement($ayat);
    }

    /**
     * Get ayats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAyats()
    {
        return $this->ayats;
    }
}
