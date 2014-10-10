<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Class PageInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface PageInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set parent
     *
     * @param PageInterface $parent
     * @return PageInterface|$this
     */
    public function setParent(PageInterface $parent = null);

    /**
     * Get parent
     *
     * @return PageInterface|$this
     */
    public function getParent();

    /**
     * Set left
     *
     * @param integer $left
     * @return PageInterface|$this
     */
    public function setLeft($left);

    /**
     * Get left
     *
     * @return integer
     */
    public function getLeft();

    /**
     * Set right
     *
     * @param integer $right
     * @return PageInterface|$this
     */
    public function setRight($right);

    /**
     * Get right
     *
     * @return integer
     */
    public function getRight();

    /**
     * Set root
     *
     * @param integer $root
     * @return PageInterface|$this
     */
    public function setRoot($root = null);

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot();

    /**
     * Set level
     *
     * @param integer $level
     * @return PageInterface|$this
     */
    public function setLevel($level);

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel();

    /**
     * Add children
     *
     * @param PageInterface $children
     * @return PageInterface|$this
     */
    public function addChild(PageInterface $children);

    /**
     * Remove children
     *
     * @param PageInterface $children
     */
    public function removeChild(PageInterface $children);

    /**
     * Has children
     *
     * @return boolean
     */
    public function hasChildren();

    /**
     * Get children
     *
     * @return ArrayCollection|PageInterface[]
     */
    public function getChildren();

    /**
     * Set name
     *
     * @param string $name
     * @return PageInterface|$this
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set title
     *
     * @param string $title
     * @return PageInterface|$this
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set route
     *
     * @param string $route
     * @return PageInterface|$this
     */
    public function setRoute($route = null);

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute();

    /**
     * Set path
     *
     * @param string $path
     * @return PageInterface|$this
     */
    public function setPath($path);

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Set static
     *
     * @param boolean $static
     * @return PageInterface|$this
     */
    public function setStatic($static);

    /**
     * Get static
     *
     * @return boolean
     */
    public function getStatic();

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return PageInterface|$this
     */
    public function setLocked($locked);

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked();

    /**
     * Set controller
     *
     * @param string $controller
     * @return PageInterface|$this
     */
    public function setController($controller = null);

    /**
     * Get controller
     *
     * @return string
     */
    public function getController();

    /**
     * Set menu
     *
     * @param boolean $menu
     * @return PageInterface|$this
     */
    public function setMenu($menu);

    /**
     * Get menu
     *
     * @return boolean
     */
    public function getMenu();

    /**
     * Set footer
     *
     * @param boolean $footer
     * @return PageInterface|$this
     */
    public function setFooter($footer);

    /**
     * Get footer
     *
     * @return boolean
     */
    public function getFooter();

    /**
     * Set advanced
     *
     * @param boolean $advanced
     * @return PageInterface|$this
     */
    public function setAdvanced($advanced);

    /**
     * Get advanced
     *
     * @return boolean
     */
    public function getAdvanced();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PageInterface|$this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return PageInterface|$this
     */
    public function setUpdatedAt(\DateTime  $updatedAt);

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
} 