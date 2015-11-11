<?php

namespace PhotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="PhotoBundle\Repository\AlbumRepository")
 * @ORM\Table(name="albums")
 */
class Album
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = 1, max = 200)
     */
    protected $caption;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = 1, max = 200)
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotBlank()
     */
    protected $enabled;

    /**
     * @ORM\Column(name="sort_index", type="integer")
     */
    protected $sortIndex;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="album")
     */
    protected $photos;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set caption
     *
     * @param string $caption
     *
     * @return Album
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Album
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Album
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add photo
     *
     * @param \PhotoBundle\Entity\Photo $photo
     *
     * @return Album
     */
    public function addPhoto(\PhotoBundle\Entity\Photo $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \PhotoBundle\Entity\Photo $photo
     */
    public function removePhoto(\PhotoBundle\Entity\Photo $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set sortIndex
     *
     * @param integer $sortIndex
     *
     * @return Album
     */
    public function setSortIndex($sortIndex)
    {
        $this->sortIndex = $sortIndex;

        return $this;
    }

    /**
     * Get sortIndex
     *
     * @return integer
     */
    public function getSortIndex()
    {
        return $this->sortIndex;
    }
}
