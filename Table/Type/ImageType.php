<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class ImageType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageType extends ResourceTableType
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $tableBuilder)
    {
        $tableBuilder
            ->addColumn('id', 'number', array(
                'sortable' => true,
            ))
            ->addColumn('image', 'image', array(
                'property_path' => 'path',
            ))
            ->addColumn('path', 'anchor', array(
                'label' => 'ekyna_core.field.path',
                'sortable' => true,
                'route_name' => 'ekyna_cms_image_admin_show',
                'route_parameters_map' => array(
                    'imageId' => 'id'
                ),
            ))
            ->addColumn('alt', 'text', array(
                'sortable' => true,
                'label' => 'ekyna_core.field.alt',
            ))
            ->addColumn('updatedAt', 'datetime', array(
                'sortable' => true,
                'label' => 'ekyna_core.field.update_date',
            ))
            ->addColumn('actions', 'admin_actions', array(
                'buttons' => array(
                    array(
                        'label' => 'ekyna_core.button.edit',
                        'class' => 'warning',
                        'route_name' => 'ekyna_cms_image_admin_edit',
                        'route_parameters_map' => array(
                            'imageId' => 'id'
                        ),
                        'permission' => 'edit',
                    ),
                    array(
                        'label' => 'ekyna_core.button.remove',
                        'class' => 'danger',
                        'route_name' => 'ekyna_cms_image_admin_remove',
                        'route_parameters_map' => array(
                            'imageId' => 'id'
                        ),
                        'permission' => 'delete',
                    ),
                ),
            ))
            ->addFilter('id', 'number')
            ->addFilter('path', 'text', array(
                'label' => 'ekyna_core.field.path'
            ))
            ->addFilter('alt', 'number', array(
                'label' => 'ekyna_core.field.alt'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_image';
    }
}
