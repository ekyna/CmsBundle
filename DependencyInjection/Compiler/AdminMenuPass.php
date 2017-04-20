<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AdminMenuPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class AdminMenuPass implements CompilerPassInterface
{
    public const GROUP = [
        'name'     => 'content',
        'label'    => 'field.content',
        'domain'   => 'EkynaUi',
        'icon'     => 'paragraph',
        'position' => 20,
    ];

    public function process(ContainerBuilder $container): void
    {
        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroup', [self::GROUP]);

        $pool->addMethodCall('createEntry', [
            'content',
            [
                'name'     => 'page',
                'resource' => 'ekyna_cms.page',
                'position' => 1,
            ],
        ]);

        $pool->addMethodCall('createEntry', [
            'content',
            [
                'name'     => 'menu',
                'resource' => 'ekyna_cms.menu',
                'position' => 70,
            ],
        ]);

        $pool->addMethodCall('createEntry', [
            'content',
            [
                'name'     => 'slide_show',
                'resource' => 'ekyna_cms.slide_show',
                'position' => 90,
            ],
        ]);

        $pool->addMethodCall('createEntry', [
            'content',
            [
                'name'     => 'tag',
                'resource' => 'ekyna_cms.tag',
                'position' => 91,
            ],
        ]);

        $pool->addMethodCall('createEntry', [
            'content',
            [
                'name'     => 'notice',
                'resource' => 'ekyna_cms.notice',
                'position' => 92,
            ],
        ]);
    }
}
