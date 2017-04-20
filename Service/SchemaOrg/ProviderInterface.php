<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

use Spatie\SchemaOrg\Type;

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
     * @return Type|null
     */
    public function build(object $object): ?Type;

    /**
     * Returns whether this provider supports the given object.
     *
     * @param object $object
     *
     * @return bool
     */
    public function supports(object $object): bool;
}
