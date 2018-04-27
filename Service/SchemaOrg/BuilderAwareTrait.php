<?php

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

/**
 * Trait BuilderAwareTrait
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait BuilderAwareTrait
{
    /**
     * @var BuilderInterface
     */
    protected $schemaBuilder;


    /**
     * Sets the schema builder.
     *
     * @param BuilderInterface $schemaBuilder
     */
    public function setSchemaBuilder(BuilderInterface $schemaBuilder)
    {
        $this->schemaBuilder = $schemaBuilder;
    }

    /**
     * Returns the schema builder.
     *
     * @return BuilderInterface
     */
    public function getSchemaBuilder()
    {
        return $this->schemaBuilder;
    }
}
