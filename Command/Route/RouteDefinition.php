<?php

namespace Ekyna\Bundle\CmsBundle\Command\Route;

use Symfony\Component\Routing\Route;

/**
 * RouteDefinition
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
     * @var boolean
     */
    protected $menu;

    /**
     * @var boolean
     */
    protected $footer;

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
     * @param string $name
     * @param Route  $route
     * 
     * @throws \InvalidArgumentException
     */
    public function __construct($routeName, Route $route)
    {
        if(null === $options = $route->getDefault('_cms')) {
            throw new \InvalidArgumentException(sprintf('Route "%s" does not have "_cms" defaults attributes.', $routeName));
        }
        if(!is_array($options) || !isset($options['name'])) {
            throw new \InvalidArgumentException(sprintf('"cms:name" must be specified for route named "%s".', $routeName));
        }

        $this->routeName = $routeName;
        $this->parentRouteName = isset($options['parent']) ? (string) $options['parent'] : null;

        $this->pageName = $options['name'];
        $this->path = $route->getPath();

        $this->locked   = isset($options['locked'])   ? (bool) $options['locked']    : true;
        $this->menu     = isset($options['menu'])     ? (bool) $options['menu']      : false;
        $this->footer   = isset($options['footer'])   ? (bool) $options['footer']    : false;
        $this->position = isset($options['position']) ? intval($options['position']) : 0;

        // TODO: other route attributes (methods, requirements ?)

        // Route with dynamic path ({parameter}) must be locked to disallow children (in backend)
        if(preg_match('#\{.*\}#', $this->path) && !$this->locked) {
            throw new \Exception('Routes with dynamic path/pattern should be locked.');
        }

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
     * Returns whether page should be displayed in main menu
     * 
     * @return boolean
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Sets whether page should be displayed in main menu
     * 
     * @return boolean
     * 
     * @return RouteDefinition
     */
    public function setMenu($menu)
    {
        $this->menu = (bool) $menu;

        return $this;
    }

    /**
     * Returns whether page should be displayed in the footer menu
     * 
     * @return boolean
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Sets whether page should be displayed in the footer menu
     * 
     * @return boolean
     * 
     * @return RouteDefinition
     */
    public function setFooter($footer)
    {
        $this->footer = (bool) $footer;

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
        if($routeDefinition->getPosition() == 0) {
            $routeDefinition->setPosition(count($this->children));
        }
        if(!$this->menu && $routeDefinition->getMenu()) {
            $routeDefinition->setMenu(false);
        }
        $this->children[$routeDefinition->getRouteName()] = $routeDefinition;

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
        if($this->hasChildren()) {
            if(isset($this->children[$routeName])) {
                return $this->children[$routeName];
            }
            foreach($this->children as $definition) {
                if(null !== $child = $definition->findChildByRouteName($routeName)) {
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
        if($this->hasChildren()) {
            foreach($this->children as $definition) {
                $definition->sortChildren();
            }
            usort($this->children, function($a, $b) {
            	if ($a->getPosition() > $b->getPosition()) {
            	    return 1;
            	} elseif ($a->getPosition() < $b->getPosition()) {
            	    return -1;
            	} else {
            	    return 0;
            	}
            });
        }
    }
}
