<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\AdminBundle\Model\AbstractTranslatable;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectTrait;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectTrait;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityTrait;
use Ekyna\Bundle\CoreBundle\Model\TimestampableTrait;

/**
 * Class Page
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method \Ekyna\Bundle\CmsBundle\Model\PageTranslationInterface translate($locale = null, $create = false)
 */
class Page extends AbstractTranslatable implements PageInterface
{
    use ContentSubjectTrait;
    use SeoSubjectTrait;
    use TimestampableTrait;
    use TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var PageInterface
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
     * @var ArrayCollection|PageInterface[]
     */
    protected $children;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $route;
    
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
    protected $advanced;

    /**
     * @var boolean
     */
    protected $dynamicPath;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->children    = new ArrayCollection();
        $this->contents    = new ArrayCollection();
        $this->seo         = new Seo();

        $this->static      = false;
        $this->locked      = false;
        $this->advanced    = false;
        $this->dynamicPath = false;
    }

    /**
     * Returns a string representation
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc
     */
    /*public function translate($locale = null, $create = false)
    {
        $this->seo->translate($locale);

        return parent::translate($locale, $create);
    }*/

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
    public function hasChild(PageInterface $child)
    {
        return $this->children->contains($child);
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(PageInterface $child)
    {
        if (!$this->hasChild($child)) {
            $this->children->add($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(PageInterface $child)
    {
        if ($this->hasChild($child)) {
            $this->children->removeElement($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return 0 < $this->children->count();
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
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function setHtml($html)
    {
        $this->translate()->setHtml($html);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        return $this->translate()->getHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->translate()->setPath($path);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->translate()->getPath();
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
    public function setDynamicPath($dynamicPath)
    {
        $this->dynamicPath = $dynamicPath;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDynamicPath()
    {
        return $this->dynamicPath;
    }

    /**
     * {@inheritdoc}
     */
    public function isIndexable()
    {
        return $this->seo->getIndex();
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.page';
    }
}
