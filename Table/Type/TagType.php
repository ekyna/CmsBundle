<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class TagType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TagType extends ResourceTableType
{
    /**
     * @inheritdoc
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('name', 'anchor', [
                'label'                => 'ekyna_core.field.name',
                'route_name'           => 'ekyna_cms_tag_admin_show',
                'route_parameters_map' => [
                    'tagId' => 'id',
                ],
                'position'             => 10,
            ])
            ->addColumn('actions', 'admin_actions', [
                'buttons'               => [
                    [
                        'label'                => 'ekyna_core.button.edit',
                        'icon'                 => 'pencil',
                        'class'                => 'warning',
                        'route_name'           => 'ekyna_cms_tag_admin_edit',
                        'route_parameters_map' => [
                            'tagId' => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                 => 'ekyna_core.button.remove',
                        'icon'                  => 'trash',
                        'class'                 => 'danger',
                        'route_name'            => 'ekyna_cms_tag_admin_remove',
                        'route_parameters_map'  => [
                            'tagId' => 'id',
                        ],
                        'permission'            => 'delete',
                    ],
                ],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ekyna_cms_tag';
    }
}
