<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

/**
 * Interface BuilderAwareInterface
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface BuilderAwareInterface
{
    /**
     * Sets the schema builder.
     *
     * @param BuilderInterface $builder
     */
    public function setSchemaBuilder(BuilderInterface $builder): void;

    /**
     * Returns the schema builder.
     *
     * @return BuilderInterface
     */
    public function getSchemaBuilder(): BuilderInterface;
}
