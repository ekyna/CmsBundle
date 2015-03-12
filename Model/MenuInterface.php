<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityInterface;
use Knp\Menu\NodeInterface;

/**
 * Interface MenuInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface MenuInterface extends NodeInterface, TaggedEntityInterface
{

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Returns the parent.
     *
     * @return MenuInterface|null
     */
    public function getParent();

    /**
     * Sets the parent.
     *
     * @param MenuInterface $parent
     * @return MenuInterface|$this
     */
    public function setParent(MenuInterface $parent = null);

    /**
     * Returns the left.
     *
     * @return int
     */
    public function getLeft();

    /**
     * Sets the left.
     *
     * @param int $left
     * @return MenuInterface|$this
     */
    public function setLeft($left);

    /**
     * Returns the right.
     *
     * @return int
     */
    public function getRight();

    /**
     * Sets the right.
     *
     * @param int $right
     * @return MenuInterface|$this
     */
    public function setRight($right);

    /**
     * Returns the root.
     *
     * @return int
     */
    public function getRoot();

    /**
     * Sets the root.
     *
     * @param int $root
     * @return MenuInterface|$this
     */
    public function setRoot($root);

    /**
     * Returns the level.
     *
     * @return int
     */
    public function getLevel();

    /**
     * Sets the level.
     *
     * @param int $level
     * @return MenuInterface|$this
     */
    public function setLevel($level);

    /**
     * {@inheritdoc}
     */
    public function getChildren();

    /**
     * Adds the child menu.
     *
     * @param MenuInterface $menu
     * @return MenuInterface|$this
     */
    public function addChild(MenuInterface $menu);

    /**
     * Removes the child menu.
     *
     * @param MenuInterface $menu
     * @return MenuInterface|$this
     */
    public function removeChild(MenuInterface $menu);

    /**
     * Returns whether the menu has children or not.
     *
     * @return boolean
     */
    public function hasChildren();

    /**
     * Sets the children.
     *
     * @param ArrayCollection|MenuInterface[] $children
     * @return MenuInterface|$this
     */
    public function setChildren(ArrayCollection $children);

    /**
     * {@inheritdoc}
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return MenuInterface|$this
     */
    public function setName($name);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return MenuInterface|$this
     */
    public function setTitle($title);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return MenuInterface|$this
     */
    public function setDescription($description);

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Sets the path.
     *
     * @param string $path
     * @return MenuInterface|$this
     */
    public function setPath($path);

    /**
     * Returns the route.
     *
     * @return string
     */
    public function getRoute();

    /**
     * Sets the route.
     *
     * @param string $route
     * @return MenuInterface|$this
     */
    public function setRoute($route);

    /**
     * Returns the route parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Sets the route parameters.
     *
     * @param array $parameters
     * @return MenuInterface|$this
     */
    public function setParameters(array $parameters = array());

    /**
     * Returns the route attributes.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Sets the route attributes.
     *
     * @param array $attributes
     * @return MenuInterface|$this
     */
    public function setAttributes(array $attributes = array());

    /**
     * Returns the locked.
     *
     * @return boolean
     */
    public function getLocked();

    /**
     * Sets the locked.
     *
     * @param boolean $locked
     * @return MenuInterface|$this
     */
    public function setLocked($locked);

    /**
     * {@inheritdoc}
     */
    public function getOptions();

    /**
     * Adds the options.
     *
     * @param array $options
     * @return MenuInterface|$this
     */
    public function addOptions(array $options);

    /**
     * Returns the page (non mapped).
     *
     * @return PageInterface|null
     */
    public function getPage();

    /**
     * Sets the page (non mapped).
     *
     * @param PageInterface $page
     * @return MenuInterface|$this
     */
    public function setPage(PageInterface $page);
}
