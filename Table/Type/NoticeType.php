<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type\Column\DateTimeType;
use Ekyna\Component\Table\TableBuilderInterface;
use Ekyna\Component\Table\Util\ColumnSort;

/**
 * Class NoticeType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class NoticeType extends ResourceTableType
{
    /**
     * @inheritdoc
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addDefaultSort('id', ColumnSort::DESC)
            ->addColumn('name', BType\Column\AnchorType::class, [
                'label'                => 'ekyna_core.field.name',
                'route_name'           => 'ekyna_cms_notice_admin_show',
                'route_parameters_map' => [
                    'noticeId' => 'id',
                ],
                'position'             => 10,
            ])
            ->addColumn('startAt', DateTimeType::class, [
                'label'    => 'ekyna_core.field.start_date',
                'position' => 20,
            ])
            ->addColumn('endAt', DateTimeType::class, [
                'label'    => 'ekyna_core.field.end_date',
                'position' => 30,
            ])
            ->addColumn('actions', BType\Column\ActionsType::class, [
                'buttons' => [
                    [
                        'label'                => 'ekyna_core.button.edit',
                        'icon'                 => 'pencil',
                        'class'                => 'warning',
                        'route_name'           => 'ekyna_cms_notice_admin_edit',
                        'route_parameters_map' => [
                            'noticeId'     => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                => 'ekyna_core.button.remove',
                        'icon'                 => 'trash',
                        'class'                => 'danger',
                        'route_name'           => 'ekyna_cms_notice_admin_remove',
                        'route_parameters_map' => [
                            'noticeId'     => 'id',
                        ],
                        'permission'           => 'delete',
                    ],
                ],
            ]);
    }
}
