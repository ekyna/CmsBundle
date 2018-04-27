<?php

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

/**
 * Interface ProviderInterface
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ProviderInterface
{
    /**
     * Builds the schema from the given object.
     *
     * @param object $object
     *
     * @return \Spatie\SchemaOrg\Type
     */
    public function build($object);

    /**
     * Returns whether this provider supports the given object.
     *
     * @param object $object
     *
     * @return bool
     */
    public function supports($object);
}
