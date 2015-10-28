<?php

namespace Ekyna\Bundle\CmsBundle;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler as Pass;
use Ekyna\Bundle\CoreBundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EkynaCmsBundle
 * @package Ekyna\Bundle\CmsBundle
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsBundle extends AbstractBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Pass\AdminMenuPass());
        $container->addCompilerPass(new Pass\EditorPluginPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Ekyna\Bundle\CmsBundle\Model\PageInterface' => 'ekyna_cms.page.class',
            'Ekyna\Bundle\CmsBundle\Model\MenuInterface' => 'ekyna_cms.menu.class',
        );
    }
}
