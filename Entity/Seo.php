<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

/**
 * Ekyna\Bundle\CmsBundle\Entity$Seo
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Seo
{
    /**
     * Returns valid changefreq choices
     * 
     * @return array
     */
    public static function getChangefreqs()
    {
        return array('hourly', 'monthly', 'yearly');
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $changefreq;

    /**
     * @var string
     */
    protected $priority;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changefreq = 'monthly';
        $this->priority = 0.5;
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
     * Set title
     *
     * @param string $title
     * @return Seo
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
     * @return Seo
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
     * Set changefreq
     *
     * @param string $changefreq
     * @return Seo
     */
    public function setChangefreq($changefreq)
    {
        $this->changefreq = $changefreq;

        return $this;
    }

    /**
     * Get changefreq
     *
     * @return string 
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * Set priority
     *
     * @param string $priority
     * @return Seo
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string 
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
