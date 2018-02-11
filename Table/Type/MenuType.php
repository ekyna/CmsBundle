<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type as CType;
use Ekyna\Component\Table\TableBuilderInterface;

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
            ->addDefaultSort('root')
            ->addDefaultSort('left')
            ->setSortable(false)
            ->setFilterable(false)
            ->setPerPageChoices([100])
            ->addColumn('title', BType\Column\NestedAnchorType::class, [
                'label'                => 'ekyna_core.field.title',
                'route_name'           => 'ekyna_cms_menu_admin_show',
                'route_parameters_map' => [
                    'menuId' => 'id',
                ],
                'position'             => 10,
            ])
            ->addColumn('name', CType\Column\TextType::class, [
                'label'    => 'ekyna_core.field.name',
                'position' => 20,
            ])
            ->addColumn('enabled', CType\Column\BooleanType::class, [
                'disable_property_path' => 'locked',
                'label'                 => 'ekyna_core.field.enabled',
                'route_name'            => 'ekyna_cms_menu_admin_toggle',
                'route_parameters'      => ['field' => 'enabled'],
                'route_parameters_map'  => ['menuId' => 'id'],
                'position'              => 30,
            ])
            ->addColumn('actions', BType\Column\NestedActionsType::class, [
                'roots'                 => true,
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
}
