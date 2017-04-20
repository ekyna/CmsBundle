<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

use Ekyna\Bundle\CmsBundle\Exception\InvalidArgumentException;
use Spatie\SchemaOrg\Type;

/**
 * Class Builder
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Builder implements BuilderInterface
{
    protected RegistryInterface $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->registry->setSchemaBuilder($this);
    }

    public function build(object $object): ?Type
    {
        try {
            return $this
                ->registry
                ->getProvider($object)
                ->build($object);
        } catch (InvalidArgumentException $e) {
        }

        return null;
    }

    public function buildScript(object $object): string
    {
        if (null !== $schema = $this->build($object)) {
            return $schema->toScript();
        }

        return '';
    }
}
