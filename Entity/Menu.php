<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Menu
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Cms\MenuTranslationInterface translate($locale = null, $create = false)
 * @method Cms\MenuTranslationInterface[] getTranslations()
 */
class Menu extends RM\AbstractTranslatable implements Cms\MenuInterface
{
    use RM\TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Menu
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
     * @var ArrayCollection|Menu[]
     */
    protected $children;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var Cms\PageInterface
     */
    protected $page;


    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->children = new ArrayCollection();
        $this->parameters = [];
        $this->attributes = [];
        $this->options = [];
        $this->locked = false;
        $this->enabled = true;
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setParent(Cms\MenuInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @inheritdoc
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @inheritdoc
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @inheritdoc
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @inheritdoc
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * @inheritdoc
     */
    public function addChild(Cms\MenuInterface $menu)
    {
        $this->children[] = $menu;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeChild(Cms\MenuInterface $menu)
    {
        $this->children->removeElement($menu);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setPath($path)
    {
        $this->translate()->setPath($path);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->translate()->getPath();
    }

    /**
     * @inheritdoc
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @inheritdoc
     */
    public function setParameters(array $parameters = [])
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @inheritdoc
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPage(Cms\PageInterface $page)
    {
        $this->page = $page;
    }

    /**
     * @inheritdoc
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @inheritdoc
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.menu';
    }
}
