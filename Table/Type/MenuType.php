<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MenuType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuType extends ResourceTableType
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('title', 'nested_anchor', array(
                'label' => 'ekyna_core.field.title',
                'sortable' => true,
                'route_name' => 'ekyna_cms_menu_admin_show',
                'route_parameters_map' => array(
                    'menuId' => 'id'
                ),
            ))
            ->addColumn('name', 'text', array(
                'label' => 'ekyna_core.field.name',
                'sortable' => true,
            ))
            ->addColumn('actions', 'admin_nested_actions', array(
                'disable_property_path' => 'locked',
                'new_child_route' => 'ekyna_cms_menu_admin_new_child',
                'move_up_route' => 'ekyna_cms_menu_admin_move_up',
                'move_down_route' => 'ekyna_cms_menu_admin_move_down',
                'routes_parameters_map' => array(
                    'menuId' => 'id'
                ),
                'buttons' => array(
                    array(
                        'label' => 'ekyna_core.button.edit',
                        'icon' => 'pencil',
                        'class' => 'warning',
                        'route_name' => 'ekyna_cms_menu_admin_edit',
                        'route_parameters_map' => array(
                            'menuId' => 'id'
                        ),
                        'permission' => 'edit',
                    ),
                    array(
                        'label' => 'ekyna_core.button.remove',
                        'icon' => 'trash',
                        'class' => 'danger',
                        'route_name' => 'ekyna_cms_menu_admin_remove',
                        'route_parameters_map' => array(
                            'menuId' => 'id'
                        ),
                        'disable_property_path' => 'locked',
                        'permission' => 'delete',
                    ),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'default_sorts' => array('root asc', 'left asc'),
            'max_per_page'  => 100,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_menu';
    }
}
