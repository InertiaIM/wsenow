<?php

namespace Inertia\WinspireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inertia\WinspireBundle\Entity\Experience
 */
class Experience
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var integer $category_id
     */
    private $category_id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var string $location
     */
    private $location;

    /**
     * @var string $image
     */
    private $image;

    /**
     * @var string $mini_image
     */
    private $mini_image;

    /**
     * @var string $logo
     */
    private $logo;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var text $detail
     */
    private $detail;

    /**
     * @var date $start_date
     */
    private $start_date;

    /**
     * @var date $end_date
     */
    private $end_date;

    /**
     * @var boolean $is_featured
     */
    private $is_featured;

    /**
     * @var boolean $is_home
     */
    private $is_home;

    /**
     * @var string $sf_id
     */
    private $sf_id;

    /**
     * @var Inertia\WinspireBundle\Entity\Category
     */
    private $category;


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
     * Set category_id
     *
     * @param integer $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
    }

    /**
     * Get category_id
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->category_id;
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
     * Set location
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set image
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     * Set mini_image
     *
     * @param string $miniImage
     */
    public function setMiniImage($miniImage)
    {
        $this->mini_image = $miniImage;
    }

    /**
     * Get mini_image
     *
     * @return string 
     */
    public function getMiniImage()
    {
        return $this->mini_image;
    }

    /**
     * Set logo
     *
     * @param string $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set detail
     *
     * @param text $detail
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    /**
     * Get detail
     *
     * @return text 
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set start_date
     *
     * @param date $startDate
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;
    }

    /**
     * Get start_date
     *
     * @return date 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param date $endDate
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;
    }

    /**
     * Get end_date
     *
     * @return date 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Set is_featured
     *
     * @param boolean $isFeatured
     */
    public function setIsFeatured($isFeatured)
    {
        $this->is_featured = $isFeatured;
    }

    /**
     * Get is_featured
     *
     * @return boolean 
     */
    public function getIsFeatured()
    {
        return $this->is_featured;
    }

    /**
     * Set is_home
     *
     * @param boolean $isHome
     */
    public function setIsHome($isHome)
    {
        $this->is_home = $isHome;
    }

    /**
     * Get is_home
     *
     * @return boolean 
     */
    public function getIsHome()
    {
        return $this->is_home;
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
     * Set category
     *
     * @param Inertia\WinspireBundle\Entity\Category $category
     */
    public function setCategory(\Inertia\WinspireBundle\Entity\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Inertia\WinspireBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
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