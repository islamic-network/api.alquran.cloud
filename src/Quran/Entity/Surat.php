<?php
// entity/Surah.php

namespace Quran\Entity;


/**
 * @Entity @Table(name="surat")
 **/
class Surat
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue 
     **/
    protected $id;
    
    /**
     * @Column(type="string", length=1000, unique=false, nullable=false)
     **/
    protected $name;
    
    /**
     * @Column(type="string", name="englishname", length=200, unique=false, nullable=false)
     **/
    protected $englishName;
    
    /**
     * @Column(type="string", name="englishtranslation", length=500, unique=false, nullable=false)
     **/
    protected $englishTranslation;
    
    /**
     * @Column(type="string", length=15, unique=false, nullable=false)
     **/
    protected $revelationCity;
    
    /**
     * @Column(type="integer", length=3, unique=false, nullable=true)
     **/
    protected $numberOfAyats;
    
    /**
     * @OneToMany(targetEntity="Ayat", mappedBy="surat")
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
     * Set name
     *
     * @param string $name
     *
     * @return Surat
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

    /**
     * Set englishName
     *
     * @param string $englishName
     *
     * @return Surat
     */
    public function setEnglishName($englishName)
    {
        $this->englishName = $englishName;

        return $this;
    }

    /**
     * Get englishName
     *
     * @return string
     */
    public function getEnglishName()
    {
        return $this->englishName;
    }

    /**
     * Set englishTranslation
     *
     * @param string $englishTranslation
     *
     * @return Surat
     */
    public function setEnglishTranslation($englishTranslation)
    {
        $this->englishTranslation = $englishTranslation;

        return $this;
    }

    /**
     * Get englishTranslation
     *
     * @return string
     */
    public function getEnglishTranslation()
    {
        return $this->englishTranslation;
    }

    /**
     * Set revelationCity
     *
     * @param string $revelationCity
     *
     * @return Surat
     */
    public function setRevelationCity($revelationCity)
    {
        $this->revelationCity = $revelationCity;

        return $this;
    }

    /**
     * Get revelationCity
     *
     * @return string
     */
    public function getRevelationCity()
    {
        return $this->revelationCity;
    }

    /**
     * Add ayat
     *
     * @param \Quran\Entity\Ayat $ayat
     *
     * @return Surat
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
    
    public function setNumberOfAyats($number)
    {
        $this->numberOfAyats = $number;
    }
    
    public function getNumberOfAyats()
    {
        return $this->numberOfAyats;
    }
}
