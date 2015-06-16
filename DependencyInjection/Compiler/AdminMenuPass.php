<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class AdminMenuPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AdminMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_admin.menu.pool')) {
            return;
        }

        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroup', array(array(
            'name'     => 'content',
            'label'    => 'ekyna_core.field.content',
            'icon'     => 'file',
            'position' => 20,
        )));
        $pool->addMethodCall('createEntry', array('content', array(
            'name'     => 'pages',
            'route'    => 'ekyna_cms_page_admin_home',
            'label'    => 'ekyna_cms.page.label.plural',
            'resource' => 'ekyna_cms_page',
            'position' => 1,
        )));
        $pool->addMethodCall('createEntry', array('content', array(
            'name'     => 'menus',
            'route'    => 'ekyna_cms_menu_admin_home',
            'label'    => 'ekyna_cms.menu.label.plural',
            'resource' => 'ekyna_cms_menu',
            'position' => 90,
        )));
        $pool->addMethodCall('createEntry', array('content', array(
            'name'     => 'tags',
            'route'    => 'ekyna_cms_tag_admin_home',
            'label'    => 'ekyna_cms.tag.label.plural',
            'resource' => 'ekyna_cms_tag',
            'position' => 100,
        )));
    }
}
