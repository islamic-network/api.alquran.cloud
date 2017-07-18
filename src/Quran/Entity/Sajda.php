<?php
// entity/Sajda.php

namespace Quran\Entity;


/**
 * @Entity @Table(name="sajda")
 **/
class Sajda
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     **/
    protected $id;

    /**
     * @OneToOne(targetEntity="Ayat", mappedBy="sajda")
     **/
    protected $ayat;

    /**
     * @Column(type="boolean")
     **/
    protected $recommended;

    /**
     * @Column(type="boolean")
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
     * @param \Quran\Entity\Ayat $ayat
     *
     * @return Sajda
     */
    public function setAyat(\Quran\Entity\Ayat $ayat)
    {
        $this->ayat = $ayat;

        return $this;
    }

    /**
     * Get ayat
     *
     * @return \Quran\Entity\Ayat
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
