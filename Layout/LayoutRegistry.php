<?php

namespace Ekyna\Bundle\CmsBundle\Layout;

/**
 * LayoutRegistry
 */
class LayoutRegistry
{
    protected $layouts;

    /**
     * Contructor
     * 
     * @param array $layouts
     */
    public function __construct(array $layouts = array())
    {
        $this->layouts = $layouts;
    }

    /**
     * Finds a layout by alias
     * 
     * @param string $alias
     * 
     * @throws \InvalidArgumentException
     */
    public function get($alias)
    {
        if(!isset($this->layouts[$alias])) {
            throw new \InvalidArgumentException(sprintf('Unknown "%s" layout.', $alias));
        }
        return $this->layouts[$alias];
    }

    /**
     * Returns all layouts
     * 
     * @return array
     */
    public function getLayouts()
    {
        return $this->layouts;
    }
}
