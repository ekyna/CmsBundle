<?php

namespace Ekyna\Bundle\CmsBundle\Event;

use Ekyna\Component\Resource\Event\ResourceEvent;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\Type;

/**
 * Class SchemaOrgEvent
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SchemaOrgEvent extends ResourceEvent
{
    /**
     * @var Schema
     */
    private $schema;


    /**
     * Returns the schema.
     *
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Sets the schema.
     *
     * @param Type $schema
     *
     * @return SchemaOrgEvent
     */
    public function setSchema(Type $schema)
    {
        $this->schema = $schema;

        return $this;
    }
}
