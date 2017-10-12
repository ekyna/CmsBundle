<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type\Column\TextType;
use Ekyna\Component\Table\TableBuilderInterface;
use Ekyna\Component\Table\Util\ColumnSort;

/**
 * Class SlideType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SlideType extends ResourceTableType
{
    /**
     * @inheritdoc
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->setSortable(false)
            ->setConfigurable(false)
            ->setBatchable(false)
            ->setExportable(false)
            ->setProfileable(false)
            ->addDefaultSort('position', ColumnSort::ASC)
            ->addColumn('name', TextType::class, [
                'label'    => 'ekyna_core.field.name',
                'position' => 10,
            ])
            ->addColumn('actions', BType\Column\ActionsType::class, [
                'buttons' => [
                    [
                        'label'                => 'ekyna_core.button.move_up',
                        'icon'                 => 'arrow-up',
                        'class'                => 'primary',
                        'route_name'           => 'ekyna_cms_slide_admin_move_up',
                        'route_parameters_map' => [
                            'slideShowId' => 'slideShow.id',
                            'slideId'     => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                => 'ekyna_core.button.move_down',
                        'icon'                 => 'arrow-down',
                        'class'                => 'primary',
                        'route_name'           => 'ekyna_cms_slide_admin_move_down',
                        'route_parameters_map' => [
                            'slideShowId' => 'slideShow.id',
                            'slideId'     => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                => 'ekyna_core.button.edit',
                        'icon'                 => 'pencil',
                        'class'                => 'warning',
                        'route_name'           => 'ekyna_cms_slide_admin_edit',
                        'route_parameters_map' => [
                            'slideShowId' => 'slideShow.id',
                            'slideId'     => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                => 'ekyna_core.button.remove',
                        'icon'                 => 'trash',
                        'class'                => 'danger',
                        'route_name'           => 'ekyna_cms_slide_admin_remove',
                        'route_parameters_map' => [
                            'slideShowId' => 'slideShow.id',
                            'slideId'     => 'id',
                        ],
                        'permission'           => 'delete',
                    ],
                ],
            ]);
    }
}
