<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MenuType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuType extends ResourceTableType
{
    /**
     * @inheritdoc
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('title', 'nested_anchor', [
                'label'                => 'ekyna_core.field.title',
                'route_name'           => 'ekyna_cms_menu_admin_show',
                'route_parameters_map' => [
                    'menuId' => 'id',
                ],
                'position'             => 10,
            ])
            ->addColumn('name', 'text', [
                'label'    => 'ekyna_core.field.name',
                'position' => 20,
            ])
            ->addColumn('enabled', 'boolean', [
                'disable_property_path' => 'locked',
                'label'                 => 'ekyna_core.field.enabled',
                'route_name'            => 'ekyna_cms_menu_admin_toggle',
                'route_parameters'      => ['field' => 'enabled'],
                'route_parameters_map'  => ['menuId' => 'id'],
                'position'              => 30,
            ])
            ->addColumn('actions', 'admin_nested_actions', [
                'disable_property_path' => 'locked',
                'new_child_route'       => 'ekyna_cms_menu_admin_new_child',
                'move_up_route'         => 'ekyna_cms_menu_admin_move_up',
                'move_down_route'       => 'ekyna_cms_menu_admin_move_down',
                'routes_parameters_map' => [
                    'menuId' => 'id',
                ],
                'buttons'               => [
                    [
                        'label'                => 'ekyna_core.button.edit',
                        'icon'                 => 'pencil',
                        'class'                => 'warning',
                        'route_name'           => 'ekyna_cms_menu_admin_edit',
                        'route_parameters_map' => [
                            'menuId' => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                 => 'ekyna_core.button.remove',
                        'icon'                  => 'trash',
                        'class'                 => 'danger',
                        'route_name'            => 'ekyna_cms_menu_admin_remove',
                        'route_parameters_map'  => [
                            'menuId' => 'id',
                        ],
                        'disable_property_path' => 'locked',
                        'permission'            => 'delete',
                    ],
                ],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'default_sorts' => ['root asc', 'left asc'],
            'max_per_page'  => 200,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ekyna_cms_menu';
    }
}
