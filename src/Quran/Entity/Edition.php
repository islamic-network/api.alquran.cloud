<?php
// entity/Edition.php

namespace Quran\Entity;


/**
 * @Entity @Table(name="edition", indexes={@Index(name="identifier_idx", columns={"identifier"}), @Index(name="language_idx", columns={"language"}), @Index(name="type_idx", columns={"type"}), @Index(name="format_idx", columns={"format"})})
 **/
class Edition
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue 
     */
    protected $id;
    
    /**
     * @Column(type="string", length=100, unique=true, nullable=false)
     **/
    protected $identifier;
    
    /**
     * @Column(type="string", length=2, unique=false, nullable=false)
     **/
    protected $language;
    
    /**
     * @Column(type="string", name="englishname", length=500, unique=false, nullable=false)
     **/
    protected $englishName;
    
    /**
     * @Column(type="string", length=1000, unique=false, nullable=false)
     **/
    protected $name;
    
    /**
     * @Column(type="string", length=50, unique=false, nullable=false)
     **/
    protected $format;
    
    /**
     * @Column(type="string", length=50, unique=false, nullable=false)
     **/
    protected $type;
    
    /**
     * @Column(type="string", length=5000, unique=false, nullable=true)
     **/
    protected $media;
    
    /**
     * @Column(type="string", length=500, unique=false, nullable=true)
     **/
    protected $source;
    
    /**
     * @Column(type="string", name="lastupdated", length=50, unique=false, nullable=true)
     **/
    protected $lastUpdated;
    
    /**
     * @OneToMany(targetEntity="Ayat", mappedBy="edition")
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
     * Set identifier
     *
     * @param string $identifier
     *
     * @return Edition
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Edition
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set englishName
     *
     * @param string $englishName
     *
     * @return Edition
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
     * Set name
     *
     * @param string $name
     *
     * @return Edition
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
     * Set format
     *
     * @param string $format
     *
     * @return Edition
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Edition
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set audio
     *
     * @param string $audio
     *
     * @return Edition
     */
    public function setAudio($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get audio
     *
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return Edition
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set lastUpdated
     *
     * @param string $lastUpdated
     *
     * @return Edition
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return string
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Add ayat
     *
     * @param \Quran\Entity\Ayat $ayat
     *
     * @return Edition
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
