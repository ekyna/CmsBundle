<?php

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Doctrine\ORM\QueryBuilder;
use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PageType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageType extends ResourceTableType
{
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
            ->addColumn('menu', 'boolean', array(
                'label' => 'ekyna_cms.field.main_menu',
                'route_name' => 'ekyna_cms_page_admin_toggle',
                'route_parameters' => array('field' => 'menu'),
                'route_parameters_map' => array('pageId' => 'id'),
            ))
            ->addColumn('footer', 'boolean', array(
                'label' => 'ekyna_cms.field.footer_menu',
                'route_name' => 'ekyna_cms_page_admin_toggle',
                'route_parameters' => array('field' => 'footer'),
                'route_parameters_map' => array('pageId' => 'id'),
            ))
            ->addColumn('actions', 'admin_nested_actions', array(
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
                        'permission' => 'edit',
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
            'default_sort' => array('left', 'asc'),
            'max_per_page'  => 100,
            'customize_qb' => function(QueryBuilder $qb, $alias) {
                $qb->select(array($alias, 's'))
                    ->innerJoin($alias.'.seo', 's');
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_page';
    }
}
