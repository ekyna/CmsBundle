<?php

namespace Ekyna\Bundle\CmsBundle;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler\AdminMenuPass;
use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler\RegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * EkynaCmsBundle
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AdminMenuPass());
        $container->addCompilerPass(new RegistryPass());
    }
}
