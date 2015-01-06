<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Knp\Menu\NodeInterface;

/**
 * Class Menu
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Menu implements NodeInterface
{
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
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * @var array
     */
    protected $options;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->parameters = [];
        $this->locked = false;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the parent.
     *
     * @return Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent.
     *
     * @param Menu $parent
     * @return Menu
     */
    public function setParent(Menu $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Returns the left.
     *
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Sets the left.
     *
     * @param int $left
     * @return Menu
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }

    /**
     * Returns the right.
     *
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Sets the right.
     *
     * @param int $right
     * @return Menu
     */
    public function setRight($right)
    {
        $this->right = $right;
        return $this;
    }

    /**
     * Returns the root.
     *
     * @return int
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Sets the root.
     *
     * @param int $root
     * @return Menu
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * Returns the level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets the level.
     *
     * @param int $level
     * @return Menu
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Returns the children.
     *
     * @return ArrayCollection|Menu[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Adds the child menu.
     *
     * @param Menu $menu
     * @return Menu
     */
    public function addChild(Menu $menu)
    {
        $this->children[] = $menu;

        return $this;
    }

    /**
     * Removes the child menu.
     *
     * @param Menu $menu
     * @return Menu
     */
    public function removeChild(Menu $menu)
    {
        $this->children->removeElement($menu);

        return $this;
    }

    /**
     * Returns whether the menu has children or not.
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * Sets the children.
     *
     * @param ArrayCollection|Menu[] $children
     * @return Menu
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;
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
     * Set name
     *
     * @param string $name
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * Set title
     *
     * @param string $title
     * @return Menu
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * Set description
     *
     * @param string $description
     * @return Menu
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path.
     *
     * @param string $path
     * @return Menu
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Returns the route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets the route.
     *
     * @param string $route
     * @return Menu
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Returns the parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the parameters.
     *
     * @param array $parameters
     * @return Menu
     */
    public function setParameters(array $parameters = array())
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Returns the locked.
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Sets the locked.
     *
     * @param boolean $locked
     * @return Menu
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (null === $this->options) {
            $this->options = array('label' => $this->getTitle());
            if (0 < strlen($this->getPath())) {
                $this->options['uri'] = $this->getPath();
            } elseif (0 < strlen($this->getRoute())) {
                $this->options['route'] = $this->getRoute();
                $parameters = $this->getParameters();
                if (!empty($parameters)) {
                    $this->options['routeParameters'] = $parameters;
                }
            }
        }
        return $this->options;
    }

    /**
     * Adds the options.
     *
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        $this->options = array_merge($this->getOptions(), $options);
        return $this;
    }
}
