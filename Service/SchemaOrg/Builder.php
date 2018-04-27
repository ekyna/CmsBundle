<?php

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

use Ekyna\Bundle\CmsBundle\Exception\InvalidArgumentException;

/**
 * Class Builder
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Builder implements BuilderInterface
{
    /**
     * @var RegistryInterface
     */
    protected $registry;


    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->registry->setSchemaBuilder($this);
    }

    /**
     * @inheritDoc
     */
    public function build($object)
    {
        try {
            return $this
                ->registry
                ->getProvider($object)
                ->build($object);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }
}
