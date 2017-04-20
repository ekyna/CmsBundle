<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

use Spatie\SchemaOrg\Type;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Interface BuilderInterface
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface BuilderInterface extends RuntimeExtensionInterface
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
     * Builds the schema script from the given object.
     *
     * @param object $object
     *
     * @return string
     */
    public function buildScript(object $object): string;
}
