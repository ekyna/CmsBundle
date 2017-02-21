<?php

namespace Ekyna\Bundle\CmsBundle;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler as Pass;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Bundle\CmsBundle\Model as CM;
use Ekyna\Bundle\ResourceBundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EkynaCmsBundle
 * @package Ekyna\Bundle\CmsBundle
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsBundle extends AbstractBundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Pass\AdminMenuPass());
        $container->addCompilerPass(new Pass\EditorPluginPass());
        $container->addCompilerPass(new Pass\WideSearchProviderPass());
    }

    /**
     * @inheritdoc
     */
    protected function getModelInterfaces()
    {
        return [
            CM\SeoInterface::class  => 'ekyna_cms.seo.class',
            CM\PageInterface::class => 'ekyna_cms.page.class',
            CM\MenuInterface::class => 'ekyna_cms.menu.class',

            EM\BlockInterface::class     => 'ekyna_cms.block.class',
            EM\ContainerInterface::class => 'ekyna_cms.container.class',
            EM\ContentInterface::class   => 'ekyna_cms.content.class',
            EM\RowInterface::class       => 'ekyna_cms.row.class',
        ];
    }
}
