<?php

namespace Ekyna\Bundle\CmsBundle;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler\AdminMenuPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ekyna\Bundle\CoreBundle\AbstractBundle;
use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler\EditorPluginPass;

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

        $container->addCompilerPass(new AdminMenuPass());
        $container->addCompilerPass(new EditorPluginPass());
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
