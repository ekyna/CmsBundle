<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Component\Table\TableBuilderInterface;
use Ekyna\Component\Table\AbstractTableType;

/**
 * PageType
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
                'label' => 'Nom',
                'route_name' => 'ekyna_page_admin_show',
                'route_parameters_map' => array(
                    'pageId' => 'id'
                ),
            ))
            ->addColumn('seo.title', 'text', array(
                'label' => 'Titre',
            ))
            ->addColumn('path', 'text', array(
                'label' => 'Url',
            ))
            ->addColumn('createdAt', 'datetime', array(
                'label' => 'Date de création',
            ))
            ->addColumn('actions', 'nested_actions', array(
                'disable_property_path' => 'locked',
                'new_child_route' => 'ekyna_page_admin_new_child',
                'move_up_route' => 'ekyna_page_admin_move_up',
                'move_down_route' => 'ekyna_page_admin_move_down',
                'routes_parameters_map' => array(
                    'pageId' => 'id'
                ),
                'buttons' => array(
                    array(
                        'label' => 'Modifier',
                        'icon' => 'pencil',
                        'class' => 'warning',
                        'route_name' => 'ekyna_page_admin_edit',
                        'route_parameters_map' => array(
                            'pageId' => 'id'
                        ),
                    ),
                    array(
                        'label' => 'Supprimer',
                        'icon' => 'trash',
                        'class' => 'danger',
                        'route_name' => 'ekyna_page_admin_remove',
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
        return 'ekyna_page';
    }
}
