<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Cmf\Component\Routing\DependencyInjection\Compiler\RegisterRoutersPass as BasePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterRoutersPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RegisterRoutersPass extends BasePass
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('ekyna_cms.router');
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        parent::process($container);

        $container->setAlias('router', 'ekyna_cms.router');
        $container->getAlias('router')->setPublic(true);
    }
}
