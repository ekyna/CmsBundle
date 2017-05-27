<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type as CType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class PageType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageType extends ResourceTableType
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
            ->addColumn('name', BType\Column\NestedAnchorType::class, [
                'label'                => 'ekyna_core.field.name',
                'route_name'           => 'ekyna_cms_page_admin_show',
                'route_parameters_map' => [
                    'pageId' => 'id',
                ],
                'position'             => 10,
            ])
            ->addColumn('enabled', CType\Column\BooleanType::class, [
                'disable_property_path' => 'static',
                'label'                 => 'ekyna_core.field.enabled',
                'route_name'            => 'ekyna_cms_page_admin_toggle',
                'route_parameters'      => ['field' => 'enabled'],
                'route_parameters_map'  => ['pageId' => 'id'],
                'position'              => 20,
            ])
            ->addColumn('actions', BType\Column\NestedActionsType::class, [
                'disable_property_path' => 'locked',
                'new_child_route'       => 'ekyna_cms_page_admin_new_child',
                'move_up_route'         => 'ekyna_cms_page_admin_move_up',
                'move_down_route'       => 'ekyna_cms_page_admin_move_down',
                'routes_parameters_map' => [
                    'pageId' => 'id',
                ],
                'buttons'               => [
                    [
                        'label'                => 'ekyna_core.button.edit',
                        'icon'                 => 'pencil',
                        'class'                => 'warning',
                        'route_name'           => 'ekyna_cms_page_admin_edit',
                        'route_parameters_map' => [
                            'pageId' => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                 => 'ekyna_core.button.remove',
                        'icon'                  => 'trash',
                        'class'                 => 'danger',
                        'route_name'            => 'ekyna_cms_page_admin_remove',
                        'route_parameters_map'  => [
                            'pageId' => 'id',
                        ],
                        'disable_property_path' => 'static',
                        'permission'            => 'delete',
                    ],
                ],
            ]);
    }
}
