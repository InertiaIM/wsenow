<?php

namespace Inertia\WinspireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inertia\WinspireBundle\Entity\Testimonial
 */
class Testimonial
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
     * @var string $title
     */
    private $title;

    /**
     * @var string $company
     */
    private $company;

    /**
     * @var text $quote
     */
    private $quote;

    /**
     * @var string $sf_id
     */
    private $sf_id;

    /**
     * @var boolean $is_dirty
     */
    private $is_dirty;

    /**
     * @var boolean $is_active
     */
    private $is_active;


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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Set company
     *
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set quote
     *
     * @param text $quote
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
    }

    /**
     * Get quote
     *
     * @return text 
     */
    public function getQuote()
    {
        return $this->quote;
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