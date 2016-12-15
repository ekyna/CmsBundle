<?php

namespace Ekyna\Bundle\CmsBundle;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler as Pass;
use Ekyna\Bundle\CoreBundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EkynaCmsBundle
 * @package Ekyna\Bundle\CmsBundle
 * @author  Étienne Dauvergne <contact@ekyna.com>
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
        $container->addCompilerPass(new Pass\WideSearchProviderPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            'Ekyna\Bundle\CmsBundle\Model\SeoInterface'  => 'ekyna_cms.seo.class',
            'Ekyna\Bundle\CmsBundle\Model\PageInterface' => 'ekyna_cms.page.class',
            'Ekyna\Bundle\CmsBundle\Model\MenuInterface' => 'ekyna_cms.menu.class',

            'Ekyna\Bundle\CmsBundle\Model\BlockInterface'     => 'ekyna_cms.block.class',
            'Ekyna\Bundle\CmsBundle\Model\ContainerInterface' => 'ekyna_cms.container.class',
            'Ekyna\Bundle\CmsBundle\Model\ContentInterface'   => 'ekyna_cms.content.class',
            'Ekyna\Bundle\CmsBundle\Model\RowInterface'       => 'ekyna_cms.row.class',
        ];
    }
}
