<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\AdminBundle\Model\TranslatableInterface;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityInterface;

/**
 * Interface MenuInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method \Ekyna\Bundle\CmsBundle\Model\MenuTranslationInterface translate($locale = null, $create = false)
 */
interface MenuInterface extends TaggedEntityInterface, TranslatableInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Sets the parent.
     *
     * @param MenuInterface $parent
     * @return MenuInterface|$this
     */
    public function setParent(MenuInterface $parent = null);

    /**
     * Returns the parent.
     *
     * @return MenuInterface|null
     */
    public function getParent();

    /**
     * Sets the left.
     *
     * @param int $left
     * @return MenuInterface|$this
     */
    public function setLeft($left);

    /**
     * Returns the left.
     *
     * @return int
     */
    public function getLeft();

    /**
     * Sets the right.
     *
     * @param int $right
     * @return MenuInterface|$this
     */
    public function setRight($right);

    /**
     * Returns the right.
     *
     * @return int
     */
    public function getRight();

    /**
     * Sets the root.
     *
     * @param int $root
     * @return MenuInterface|$this
     */
    public function setRoot($root);

    /**
     * Returns the root.
     *
     * @return int
     */
    public function getRoot();

    /**
     * Sets the level.
     *
     * @param int $level
     * @return MenuInterface|$this
     */
    public function setLevel($level);

    /**
     * Returns the level.
     *
     * @return int
     */
    public function getLevel();

    /**
     * Sets the children.
     *
     * @param ArrayCollection|MenuInterface[] $children
     * @return MenuInterface|$this
     */
    public function setChildren(ArrayCollection $children);

    /**
     * Returns whether the menu has children or not.
     *
     * @return boolean
     */
    public function hasChildren();

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
     * {@inheritdoc}
     */
    public function getChildren();

    /**
     * Set name
     *
     * @param string $name
     * @return MenuInterface|$this
     */
    public function setName($name);

    /**
     * {@inheritdoc}
     */
    public function getName();

    /**
     * Set title
     *
     * @param string $title
     * @return MenuInterface|$this
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set description
     *
     * @param string $description
     * @return MenuInterface|$this
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the path.
     *
     * @param string $path
     * @return MenuInterface|$this
     */
    public function setPath($path);

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Sets the route.
     *
     * @param string $route
     * @return MenuInterface|$this
     */
    public function setRoute($route);

    /**
     * Returns the route.
     *
     * @return string
     */
    public function getRoute();

    /**
     * Sets the route parameters.
     *
     * @param array $parameters
     * @return MenuInterface|$this
     */
    public function setParameters(array $parameters = array());

    /**
     * Returns the route parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Sets the route attributes.
     *
     * @param array $attributes
     * @return MenuInterface|$this
     */
    public function setAttributes(array $attributes = array());

    /**
     * Returns the route attributes.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Sets the locked.
     *
     * @param boolean $locked
     * @return MenuInterface|$this
     */
    public function setLocked($locked);

    /**
     * Returns the locked.
     *
     * @return boolean
     */
    public function getLocked();

    /**
     * Sets the page (non mapped).
     *
     * @param PageInterface $page
     * @return MenuInterface|$this
     */
    public function setPage(PageInterface $page);

    /**
     * Returns the page (non mapped).
     *
     * @return PageInterface|null
     */
    public function getPage();
}
