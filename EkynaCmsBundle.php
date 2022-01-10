<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler as Pass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EkynaCmsBundle
 * @package Ekyna\Bundle\CmsBundle
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new Pass\AdminMenuPass());
        $container->addCompilerPass(new Pass\ChainRouterPass());
        $container->addCompilerPass(new Pass\EditorPluginPass());
        $container->addCompilerPass(new Pass\SchemaOrgProviderPass());
    }
}
