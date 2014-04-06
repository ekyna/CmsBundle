<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectTrait;

/**
 * Page
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Page implements ContentSubjectInterface
{
    use ContentSubjectTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    protected $parent;

    /**
     * @var integer
     */
    protected $left;

    /**
     * @var integer
     */
    protected $right;

    /**
     * @var integer
     */
    protected $root;

    /**
     * @var integer
     */
    protected $level;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $children;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $menu;

    /**
     * @var boolean
     */
    protected $footer;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\Seo
     */
    protected $seo;

    /**
     * @var string
     */
    protected $route;
    
    /**
     * @var string
     */
    protected $path;
    
    /**
     * @var boolean
     */
    protected $static;
    
    /**
     * @var boolean
     */
    protected $locked;
    
    /**
     * @var string
     */
    protected $controller;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->contents = new ArrayCollection();
        $this->setSeo(new Seo());
        $this->static = false;
        $this->locked = false;
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
     * Set parent
     *
     * @param \Ekyna\Bundle\CmsBundle\Entity\Page $parent
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setParent(Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set left
     *
     * @param integer $left
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * Get left
     *
     * @return integer 
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set right
     *
     * @param integer $right
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * Get right
     *
     * @return integer 
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setRoot($root = null)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Add children
     *
     * @param \Ekyna\Bundle\CmsBundle\Entity\Page $children
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function addChild(Page $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Ekyna\Bundle\CmsBundle\Entity\Page $children
     */
    public function removeChild(Page $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Has children
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
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
     * Set menu
     *
     * @param boolean $menu
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setMenu($menu)
    {
        $this->menu = (bool) $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return boolean
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set footer
     *
     * @param boolean $footer
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setFooter($footer)
    {
        $this->footer = (bool) $footer;

        return $this;
    }

    /**
     * Get footer
     *
     * @return boolean
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set seo
     *
     * @param \Ekyna\Bundle\CmsBundle\Entity\Seo $seo
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setSeo(Seo $seo)
    {
        $seo->setPage($this);
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\Seo
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setRoute($route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set static
     *
     * @param boolean $static
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setStatic($static)
    {
        $this->static = $static;

        return $this;
    }

    /**
     * Get static
     *
     * @return boolean 
     */
    public function getStatic()
    {
        return $this->static;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set controller
     *
     * @param string $controller
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setController($controller = null)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller
     *
     * @return string 
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function setUpdatedAt(\DateTime  $updatedAt)
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
}
