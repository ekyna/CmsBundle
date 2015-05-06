<?php

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

/**
 * Class RouteDefinition
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class RouteDefinition
{
    /**
     * @var string
     */
    protected $routeName;

    /**
     * @var string
     */
    protected $parentRouteName;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $pageName;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * @var array
     */
    protected $menus;

    /**
     * @var boolean
     */
    protected $advanced;

    /**
     * @var array
     */
    protected $seo;

    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @var array
     */
    protected $children;

    /**
     * Constructor
     *
     * @param string $routeName
     * @param array $options
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __construct($routeName, array $options)
    {
        $this->routeName = $routeName;
        $this->parentRouteName = $options['parent'];

        $this->pageName = $options['name'];
        $this->path     = '/'.trim($options['path'], '/');
        $this->locked   = $options['locked'];
        $this->menus    = $options['menus'];
        $this->advanced = $options['advanced'];
        $this->position = $options['position'];
        $this->seo      = $options['seo'];

        $this->children = array();
    }

    /**
     * Returns the route name
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Returns the parent route name
     *
     * @return string
     */
    public function getParentRouteName()
    {
        return $this->parentRouteName;
    }

    /**
     * Sets the parent route name
     *
     * @param string
     *
     * @return RouteDefinition
     */
    public function setParentRouteName($name)
    {
        $this->parentRouteName = $name;

        return $this;
    }

    /**
     * Returns the page name
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * Returns the route path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns whether page should be locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Returns the menus.
     *
     * @return array
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Sets the menus.
     *
     * @param array $menus
     * @return RouteDefinition
     */
    public function setMenus(array $menus = array())
    {
        $this->menus = $menus;
        return $this;
    }

    /**
     * Returns whether page has an advanced content
     *
     * @return boolean
     */
    public function getAdvanced()
    {
        return $this->advanced;
    }

    /**
     * Sets whether page has an advanced content
     *
     * @param boolean $advanced
     *
     * @return RouteDefinition
     */
    public function setAdvanced($advanced)
    {
        $this->advanced = (bool)$advanced;

        return $this;
    }

    /**
     * Returns the seo.
     *
     * @return array
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Sets the seo.
     *
     * @param array $seo
     * @return RouteDefinition
     */
    public function setSeo(array $seo)
    {
        $this->seo = $seo;
        return $this;
    }

    /**
     * Returns the position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the position
     *
     * @param integer
     *
     * @return RouteDefinition
     */
    public function setPosition($position)
    {
        $this->position = intval($position);

        return $this;
    }

    /**
     * Adds a child route definition
     *
     * @param RouteDefinition $routeDefinition
     *
     * @return RouteDefinition
     */
    public function appendChild(RouteDefinition $routeDefinition)
    {
        if ($routeDefinition->getPosition() == 0) {
            $routeDefinition->setPosition(count($this->children));
        }
        $seo = $routeDefinition->getSeo();
        if (!$this->seo['follow'] && $seo['follow']) {
            $seo['follow'] = false;
            $routeDefinition->setSeo($seo);
        }
        if (!$this->seo['index'] && $seo['index']) {
            $seo['index'] = false;
            $routeDefinition->setSeo($seo);
        }
        $routeName = $routeDefinition->getRouteName();
        if (array_key_exists($routeName, $this->children)) {
            throw new \LogicException(sprintf('Route "%s" already exists.', $routeName));
        }
        $this->children[$routeName] = $routeDefinition;

        return $this;
    }

    /**
     * Returns children routes
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Returns whether the definition has children definitions
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * Returns a child definition
     *
     * @param string $routeName
     *
     * @return RouteDefinition|NULL
     */
    public function findChildByRouteName($routeName)
    {
        if ($this->hasChildren()) {
            if (array_key_exists($routeName, $this->children)) {
                return $this->children[$routeName];
            }
            /** @var RouteDefinition $definition */
            foreach ($this->children as $definition) {
                if (null !== $child = $definition->findChildByRouteName($routeName)) {
                    return $child;
                }
            }
        }
        return null;
    }

    /**
     * Sorts children definitions by position
     */
    public function sortChildren()
    {
        if ($this->hasChildren()) {
            /** @var RouteDefinition $definition */
            foreach ($this->children as $definition) {
                $definition->sortChildren();
            }
            uasort($this->children, function ($a, $b) {
                /** @var RouteDefinition $a */
                /** @var RouteDefinition $b */
                if ($a->getPosition() == $b->getPosition()) {
                    return 0;
                }
                return $a->getPosition() < $b->getPosition() ? -1 : 1;
            });
        }
    }
}
