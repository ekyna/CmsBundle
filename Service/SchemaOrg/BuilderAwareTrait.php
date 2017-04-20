<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

/**
 * Trait BuilderAwareTrait
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait BuilderAwareTrait
{
    protected BuilderInterface $schemaBuilder;


    /**
     * Sets the schema builder.
     *
     * @param BuilderInterface $schemaBuilder
     */
    public function setSchemaBuilder(BuilderInterface $schemaBuilder): void
    {
        $this->schemaBuilder = $schemaBuilder;
    }

    /**
     * Returns the schema builder.
     *
     * @return BuilderInterface
     */
    public function getSchemaBuilder(): BuilderInterface
    {
        return $this->schemaBuilder;
    }
}
