<?php

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

/**
 * Interface BuilderInterface
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface BuilderInterface
{
    /**
     * Builds the schema from the given object.
     *
     * @param object $object
     *
     * @return \Spatie\SchemaOrg\Type
     */
    public function build($object);
}
