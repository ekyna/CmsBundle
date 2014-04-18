<?php

namespace Ekyna\Bundle\CmsBundle\Layout;

/**
 * Layout
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Layout
{
    protected $id;

    protected $name;

    protected $configuration;

    public function __construct($id, $name, $configuration)
    {
        $this->id = $id;
        $this->name = $name;
        $this->configuration = $configuration;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }
}
