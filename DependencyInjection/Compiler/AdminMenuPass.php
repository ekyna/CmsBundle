<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AdminMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_admin.menu.pool')) {
            return;
        }

        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroupReference', array(
            'content', 'ekyna.content.label', 'file', null, 98
        ));
        $pool->addMethodCall('createEntryReference', array(
            'content', 'pages', 'ekyna_cms_page_admin_home', 'ekyna_cms.page.label.plural'
        ));
    }
}