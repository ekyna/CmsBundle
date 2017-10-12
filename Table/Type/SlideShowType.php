<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class SlideShowType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SlideShowType extends ResourceTableType
{
    /**
     * @inheritdoc
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('name', BType\Column\AnchorType::class, [
                'label'                => 'ekyna_core.field.name',
                'route_name'           => 'ekyna_cms_slide_show_admin_show',
                'route_parameters_map' => [
                    'slideShowId' => 'id',
                ],
                'position'             => 10,
            ])
            ->addColumn('actions', BType\Column\ActionsType::class, [
                'buttons' => [
                    [
                        'label'                => 'ekyna_core.button.edit',
                        'icon'                 => 'pencil',
                        'class'                => 'warning',
                        'route_name'           => 'ekyna_cms_slide_show_admin_edit',
                        'route_parameters_map' => [
                            'slideShowId' => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                 => 'ekyna_core.button.remove',
                        'icon'                  => 'trash',
                        'class'                 => 'danger',
                        'route_name'            => 'ekyna_cms_slide_show_admin_remove',
                        'route_parameters_map'  => [
                            'slideShowId' => 'id',
                        ],
                        'disable_property_path' => 'tag',
                        'permission'            => 'delete',
                    ],
                ],
            ]);
    }
}
