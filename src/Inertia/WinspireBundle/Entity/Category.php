<?php

namespace Inertia\WinspireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inertia\WinspireBundle\Entity\Category
 */
class Category
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $sf_id
     */
    private $sf_id;

    /**
     * @var Inertia\WinspireBundle\Entity\Experience
     */
    private $experience;

    public function __construct()
    {
        $this->experience = new \Doctrine\Common\Collections\ArrayCollection();
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set sf_id
     *
     * @param string $sfId
     */
    public function setSfId($sfId)
    {
        $this->sf_id = $sfId;
    }

    /**
     * Get sf_id
     *
     * @return string 
     */
    public function getSfId()
    {
        return $this->sf_id;
    }

    /**
     * Add experience
     *
     * @param Inertia\WinspireBundle\Entity\Experience $experience
     */
    public function addExperience(\Inertia\WinspireBundle\Entity\Experience $experience)
    {
        $this->experience[] = $experience;
    }

    /**
     * Get experience
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getExperience()
    {
        return $this->experience;
    }
    /**
     * @var string $slug
     */
    private $slug;


    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
    /**
     * @var boolean $is_dirty
     */
    private $is_dirty;

    /**
     * @var boolean $is_active
     */
    private $is_active;


    /**
     * Set is_dirty
     *
     * @param boolean $isDirty
     */
    public function setIsDirty($isDirty)
    {
        $this->is_dirty = $isDirty;
    }

    /**
     * Get is_dirty
     *
     * @return boolean 
     */
    public function getIsDirty()
    {
        return $this->is_dirty;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }
}