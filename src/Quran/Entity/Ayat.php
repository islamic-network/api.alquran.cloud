<?php
// entity/Ayat.php

namespace Quran\Entity;


/**
 * @Entity @Table(name="ayat", indexes={@Index(name="surat_idx", columns={"surat_id"}), @Index(name="edition_idx", columns={"edition_id"}), @Index(name="juz_idx", columns={"juz_id"}), @Index(name="number_idx", columns={"number"}), @Index(name="numberinsurat_idx", columns={"numberinsurat"})})
 **/
class Ayat
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue 
     **/
    protected $id;
    
    /**
     * @Column(type="integer", length=4, nullable=false)
     **/
    protected $number;
    
    /**
     * @Column(type="integer", name="numberinsurat", length=4, nullable=false)
     **/
    protected $numberInSurat;
    
    /**
     * @Column(type="string", length=64000, nullable=false)
     **/
    protected $text;
    
    /**
     * @ManyToOne(targetEntity="Surat", inversedBy="ayats")
     **/
    protected $surat;
    
    /**
     * @ManyToOne(targetEntity="Edition", inversedBy="ayats")
     **/
    protected $edition;
    
    /**
     * @ManyToOne(targetEntity="Juz", inversedBy="ayats")
     **/
    protected $juz;



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
     * Set number
     *
     * @param integer $number
     *
     * @return Ayat
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }
    
    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get number in Surat
     *
     * @return integer
     */
    public function getNumberInSurat()
    {
        return $this->numberInSurat;
    }
    
    /**
     * Set number in Surat
     *
     * @param integer $number
     *
     * @return Ayat
     */
    public function setNumberInSurat($number)
    {
        $this->numberInSurat = $number;

        return $this;
    }

    

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Ayat
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set surat
     *
     * @param \Quran\Entity\Surat $surat
     *
     * @return Ayat
     */
    public function setSurat(\Quran\Entity\Surat $surat = null)
    {
        $this->surat = $surat;

        return $this;
    }

    /**
     * Get surat
     *
     * @return \Quran\Entity\Surat
     */
    public function getSurat()
    {
        return $this->surat;
    }

    /**
     * Set edition
     *
     * @param \Quran\Entity\Edition $edition
     *
     * @return Ayat
     */
    public function setEdition(\Quran\Entity\Edition $edition = null)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return \Quran\Entity\Edition
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Set juz
     *
     * @param \Quran\Entity\Juz $juz
     *
     * @return Ayat
     */
    public function setJuz(\Quran\Entity\Juz $juz = null)
    {
        $this->juz = $juz;

        return $this;
    }

    /**
     * Get juz
     *
     * @return \Quran\Entity\Juz
     */
    public function getJuz()
    {
        return $this->juz;
    }
}
