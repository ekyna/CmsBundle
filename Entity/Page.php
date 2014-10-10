<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectTrait;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectTrait;

/**
 * Class Page
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Page implements PageInterface, ContentSubjectInterface, SeoSubjectInterface
{
    use ContentSubjectTrait;
    use SeoSubjectTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Page
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
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $html;

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
     * @var boolean
     */
    protected $menu;

    /**
     * @var boolean
     */
    protected $footer;

    /**
     * @var boolean
     */
    protected $advanced;

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
        $this->menu = false;
        $this->footer = false;
        $this->advanced = false;
    }

    /**
     * Returns a string representation
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(PageInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * {@inheritdoc}
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoot($root = null)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(PageInterface $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(PageInterface $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoute($route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatic($static)
    {
        $this->static = $static;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatic()
    {
        return $this->static;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller = null)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * {@inheritdoc}
     */
    public function setMenu($menu)
    {
        $this->menu = (bool) $menu;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * {@inheritdoc}
     */
    public function setFooter($footer)
    {
        $this->footer = (bool) $footer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdvanced($advanced)
    {
        $this->advanced = (bool) $advanced;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvanced()
    {
        return $this->advanced;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime  $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
