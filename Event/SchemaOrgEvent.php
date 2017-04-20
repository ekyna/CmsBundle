<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Event;

use Ekyna\Component\Resource\Event\ResourceEvent;
use Spatie\SchemaOrg\Type;

/**
 * Class SchemaOrgEvent
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
final class SchemaOrgEvent extends ResourceEvent
{
    private ?Type $schema = null;

    public function getSchema(): ?Type
    {
        return $this->schema;
    }

    public function setSchema(Type $schema): SchemaOrgEvent
    {
        $this->schema = $schema;

        return $this;
    }
}
