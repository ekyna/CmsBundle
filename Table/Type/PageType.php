<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Component\Table\TableBuilderInterface;
use Ekyna\Component\Table\AbstractTableType;

/**
 * PageType
 * 
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageType extends AbstractTableType
{
    protected $entityClass;

    public function __construct($class)
    {
        $this->entityClass = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $tableBuilder)
    {
        $tableBuilder
            ->addColumn('name', 'nested_anchor', array(
                'label' => 'ekyna_core.field.name',
                'route_name' => 'ekyna_cms_page_admin_show',
                'route_parameters_map' => array(
                    'pageId' => 'id'
                ),
            ))
            ->addColumn('seo.title', 'text', array(
                'label' => 'ekyna_core.field.title',
            ))
            ->addColumn('path', 'text', array(
                'label' => 'ekyna_core.field.url',
            ))
            ->addColumn('updatedAt', 'datetime', array(
                'label' => 'ekyna_core.field.update_date',
            ))
            ->addColumn('actions', 'nested_actions', array(
                'disable_property_path' => 'locked',
                'new_child_route' => 'ekyna_cms_page_admin_new_child',
                'move_up_route' => 'ekyna_cms_page_admin_move_up',
                'move_down_route' => 'ekyna_cms_page_admin_move_down',
                'routes_parameters_map' => array(
                    'pageId' => 'id'
                ),
                'buttons' => array(
                    array(
                        'label' => 'ekyna_core.button.edit',
                        'icon' => 'pencil',
                        'class' => 'warning',
                        'route_name' => 'ekyna_cms_page_admin_edit',
                        'route_parameters_map' => array(
                            'pageId' => 'id'
                        ),
                    ),
                    array(
                        'label' => 'ekyna_core.button.remove',
                        'icon' => 'trash',
                        'class' => 'danger',
                        'route_name' => 'ekyna_cms_page_admin_remove',
                        'route_parameters_map' => array(
                            'pageId' => 'id'
                        ),
                        'disable_property_path' => 'static',
                    ),
                ),
            ))
            ->setDefaultSort('left')
            ->setMaxPerPage(50)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_page';
    }
}
