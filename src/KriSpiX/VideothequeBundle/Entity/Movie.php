<?php

namespace KriSpiX\VideothequeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Movie
 *
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="KriSpiX\VideothequeBundle\Repository\MovieRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("ean")
 */
class Movie
{
    const NB_PER_PAGE = 12;
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Length(min=2)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="overview", type="text", nullable=true)
     * @Assert\Length(min=10)
     */
    private $overview;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="movie_date", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $movieDate;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="ean", type="string", length=13, unique=true)
     * @Assert\Length(min=13)
     * @Assert\Length(max=13)
     */
    private $ean;

    /**
     * @var bool
     *
     * @ORM\Column(name="lend", type="boolean")
     */
    private $lend = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="see", type="boolean")
     */
    private $see = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchase_date", type="datetime")
     * @Assert\DateTime()
     */
    private $purchaseDate;

    /**
    * @ORM\ManyToMany(targetEntity="KriSpiX\VideothequeBundle\Entity\Genre", indexBy="id", cascade={"persist"})
    */
    private $genres;
    
    /**
    * @ORM\ManyToMany(targetEntity="KriSpiX\VideothequeBundle\Entity\Keyword", indexBy="id", cascade={"persist"})
    */

    private $keywords;
    
    /**
    * @ORM\ManyToOne(targetEntity="KriSpiX\VideothequeBundle\Entity\Format")
    */
    private $format;

    /**
    * @ORM\Column(name="updated_at", type="datetime", nullable=true)
    */
    private $updatedAt;
    
    /**
    * @Gedmo\Slug(fields={"title"})
    * @ORM\Column(length=128, unique=true)
    */
    private $slug;
    
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
     * Set title
     *
     * @param string $title
     * @return Movie
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set overview
     *
     * @param string $overview
     * @return Movie
     */
    public function setOverview($overview)
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * Get overview
     *
     * @return string 
     */
    public function getOverview()
    {
        return $this->overview;
    }

    /**
     * Set movieDate
     *
     * @param \DateTime $movieDate
     * @return Movie
     */
    public function setMovieDate($movieDate)
    {
        $this->movieDate = $movieDate;

        return $this;
    }

    /**
     * Get movieDate
     *
     * @return \DateTime 
     */
    public function getMovieDate()
    {
        return $this->movieDate;
    }

    /**
     * Set allocineLink
     *
     * @param string $allocineLink
     * @return Movie
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get allocineLink
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Movie
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set ean
     *
     * @param string $ean
     * @return Movie
     */
    public function setEan($ean)
    {
        $this->ean = $ean;

        return $this;
    }

    /**
     * Get ean
     *
     * @return string 
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * Set lend
     *
     * @param boolean $lend
     * @return Movie
     */
    public function setLend($lend)
    {
        $this->lend = $lend;

        return $this;
    }

    /**
     * Get lend
     *
     * @return boolean 
     */
    public function getLend()
    {
        return $this->lend;
    }

    /**
     * Set see
     *
     * @param boolean $see
     * @return Movie
     */
    public function setSee($see)
    {
        $this->see = $see;

        return $this;
    }

    /**
     * Get see
     *
     * @return boolean 
     */
    public function getSee()
    {
        return $this->see;
    }

    /**
     * Set purchaseDate
     *
     * @param \DateTime $purchaseDate
     * @return Movie
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return \DateTime 
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->keywords = new \Doctrine\Common\Collections\ArrayCollection();
        $this->purchaseDate = new \Datetime();
    }

    /**
     * Add genres
     *
     * @param \KriSpiX\VideothequeBundle\Entity\Genre $genres
     * @return Movie
     */
    public function addGenre(\KriSpiX\VideothequeBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \KriSpiX\VideothequeBundle\Entity\Genre $genres
     */
    public function removeGenre(\KriSpiX\VideothequeBundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Add keywords
     *
     * @param \KriSpiX\VideothequeBundle\Entity\Keyword $keywords
     * @return Movie
     */
    public function addKeyword(\KriSpiX\VideothequeBundle\Entity\Keyword $keywords)
    {
        $this->keywords[] = $keywords;

        return $this;
    }

    /**
     * Remove keywords
     *
     * @param \KriSpiX\VideothequeBundle\Entity\Keyword $keywords
     */
    public function removeKeyword(\KriSpiX\VideothequeBundle\Entity\Keyword $keywords)
    {
        $this->keywords->removeElement($keywords);
    }

    /**
     * Get keywords
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set format
     *
     * @param \KriSpiX\VideothequeBundle\Entity\Format $format
     * @return Movie
     */
    public function setFormat(\KriSpiX\VideothequeBundle\Entity\Format $format = null)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return \KriSpiX\VideothequeBundle\Entity\Format 
     */
    public function getFormat()
    {
        return $this->format;
    }
    
    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Movie
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
    * @ORM\PreUpdate
    */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Movie
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
