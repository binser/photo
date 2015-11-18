<?php

namespace PhotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PhotoBundle\Repository\PostRepository")
 * @ORM\Table(name="posts")
 */
class Post
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="date_create", type="datetime")
     */
    protected $dateCreate;

    /**
     * @ORM\Column(name="update_create", type="datetime")
     */
    protected $updateCreate;

    /**
     * @ORM\Column(name="title", type="string")
     */
    protected $title;

    /**
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @ORM\Column(name="text", type="text")
     */
    protected $text;


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
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     *
     * @return Post
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set updateCreate
     *
     * @param \DateTime $updateCreate
     *
     * @return Post
     */
    public function setUpdateCreate($updateCreate)
    {
        $this->updateCreate = $updateCreate;

        return $this;
    }

    /**
     * Get updateCreate
     *
     * @return \DateTime
     */
    public function getUpdateCreate()
    {
        return $this->updateCreate;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
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
     * Set description
     *
     * @param string $description
     *
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Post
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
}
