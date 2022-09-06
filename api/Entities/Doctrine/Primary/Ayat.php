<?php

// api/Entities/Doctrine/Primary/Ayat.php

namespace Api\Entities\Doctrine\Primary;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(name="ayat", indexes={@ORM\Index(name="surat_idx", columns={"surat_id"}), @ORM\Index(name="edition_idx", columns={"edition_id"}), @ORM\Index(name="juz_idx", columns={"juz_id"}), @ORM\Index(name="number_idx", columns={"number"}), @ORM\Index(name="numberinsurat_idx", columns={"numberinsurat"})})
 */
class Ayat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     **/
    protected $id;

    /**
     * @ORM\Column(type="integer", length=4, nullable=false)
     **/
    protected $number;

    /**
     * @ORM\Column(type="integer", name="numberinsurat", length=4, nullable=false)
     **/
    protected $numberInSurat;

    /**
     * @ORM\Column(type="string", length=64000, nullable=false)
     **/
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="Surat", inversedBy="ayats")
     **/
    protected $surat;

    /**
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="ayats")
     **/
    protected $edition;

    /**
     * @ORM\ManyToOne(targetEntity="Juz", inversedBy="ayats")
     **/
    protected $juz;

    /**
     * @ORM\ManyToOne(targetEntity="Manzil", inversedBy="ayats")
     **/
    protected $manzil;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="ayats")
     **/
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="HizbQuarter", inversedBy="ayats")
     **/
    protected $hizbQuarter;

    /**
     * @ORM\ManyToOne(targetEntity="Ruku", inversedBy="ayats")
     **/
    protected $ruku;

    /**
     * @ORM\ManyToOne(targetEntity="Sajda", inversedBy="ayats")
     **/
    protected $sajda;

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
     * @param Surat $surat
     *
     * @return Ayat
     */
    public function setSurat(Surat $surat = null)
    {
        $this->surat = $surat;

        return $this;
    }

    /**
     * Get surat
     *
     * @return Surat
     */
    public function getSurat()
    {
        return $this->surat;
    }

    /**
     * Set edition
     *
     * @param Edition $edition
     *
     * @return Ayat
     */
    public function setEdition(Edition $edition = null)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return Edition
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Set juz
     *
     * @param Juz $juz
     *
     * @return Ayat
     */
    public function setJuz(Juz $juz = null)
    {
        $this->juz = $juz;

        return $this;
    }

    /**
     * Get juz
     *
     * @return Juz
     */
    public function getJuz()
    {
        return $this->juz;
    }

    /**
     * Set manzil
     *
     * @param Manzil $manzil
     *
     * @return Ayat
     */
    public function setManzil(Manzil $manzil = null)
    {
        $this->manzil = $manzil;

        return $this;
    }

    /**
     * Get manzil
     *
     * @return Manzil
     */
    public function getManzil()
    {
        return $this->manzil;
    }

    /**
     * Set page
     *
     * @param Page $page
     *
     * @return Ayat
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Api\Entities\Doctrine\Primary\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set ruku
     *
     * @param \Api\Entities\Doctrine\Primary\Ruku $ruku
     *
     * @return Ayat
     */
    public function setRuku(\Api\Entities\Doctrine\Primary\Ruku $ruku = null)
    {
        $this->ruku = $ruku;

        return $this;
    }

    /**
     * Get ruku
     *
     * @return \Api\Entities\Doctrine\Primary\Ruku
     */
    public function getRuku()
    {
        return $this->ruku;
    }

    /**
     * Set hizbQuarter
     *
     * @param \Api\Entities\Doctrine\Primary\HizbQuarter $hizbQuarter
     *
     * @return Ayat
     */
    public function setHizbQuarter(\Api\Entities\Doctrine\Primary\HizbQuarter $hizbQuarter = null)
    {
        $this->hizbQuarter = $hizbQuarter;

        return $this;
    }

    /**
     * Get hizbQuarter
     *
     * @return \Api\Entities\Doctrine\Primary\HizbQuarter
     */
    public function getHizbQuarter()
    {
        return $this->hizbQuarter;
    }

    /**
     * Set sajda
     *
     * @param \Api\Entities\Doctrine\Primary\Sajda $sajda
     *
     * @return Ayat
     */
    public function setSajda(\Api\Entities\Doctrine\Primary\Sajda $sajda = null)
    {
        $this->sajda = $sajda;

        return $this;
    }

    /**
     * Get sajda
     *
     * @return \Api\Entities\Doctrine\Primary\Sajda
     */
    public function getSajda()
    {
        return $this->sajda;
    }

}
