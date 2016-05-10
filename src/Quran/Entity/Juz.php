<?php
// entity/Juz.php

namespace Quran\Entity;


/**
 * @Entity @Table(name="juz")
 **/
class Juz
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue 
     **/
    protected $id;
    
    /**
     * @OneToMany(targetEntity="Ayat", mappedBy="juz")
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
     * @param \Quran\Entity\Ayat $ayat
     *
     * @return Juz
     */
    public function addAyat(\Quran\Entity\Ayat $ayat)
    {
        $this->ayats[] = $ayat;

        return $this;
    }

    /**
     * Remove ayat
     *
     * @param \Quran\Entity\Ayat $ayat
     */
    public function removeAyat(\Quran\Entity\Ayat $ayat)
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
