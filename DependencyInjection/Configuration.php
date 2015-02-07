<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_cms');

        $rootNode
            ->children()
                ->scalarNode('output_dir')->defaultValue('')->cannotBeEmpty()->end()
                ->booleanNode('esi_flashes')->defaultFalse()->end()
                ->scalarNode('home_route')->defaultValue('home')->cannotBeEmpty()->end()
                ->arrayNode('seo')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('no_follow')->defaultFalse()->end()
                        ->booleanNode('no_index')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('page')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('controllers')
                            ->defaultValue(array('default' => array(
                                'title' => 'Par défaut',
                                'value' => 'EkynaCmsBundle:Cms:default',
                            )))
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('value')->isRequired()->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('menu')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('roots')
                            ->defaultValue(array('main' => array(
                                'title' => 'Navigation principale',
                                'description' => 'Barre de navigation principale',
                            )))
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('description')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addPoolsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `pools` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPoolsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('pools')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    '_form.html'     => 'EkynaCmsBundle:Admin/Page:_form.html',
                                    'list.html'      => 'EkynaCmsBundle:Admin/Page:list.html',
                                    'new.html'       => 'EkynaCmsBundle:Admin/Page:new.html',
                                    'new_child.html' => 'EkynaCmsBundle:Admin/Page:new_child.html',
                                    'show.html'      => 'EkynaCmsBundle:Admin/Page:show.html',
                                    'edit.html'      => 'EkynaCmsBundle:Admin/Page:edit.html',
                                    'remove.html'    => 'EkynaCmsBundle:Admin/Page:remove.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Page')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\PageController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\PageRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\PageType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\PageType')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('event')->defaultValue('Ekyna\Bundle\CmsBundle\Event\PageEvent')->end()
                            ->end()
                        ->end()
                        ->arrayNode('file')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    'show.html'  => 'EkynaCmsBundle:Admin/File:show.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\File')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\FileType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\FileType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                        ->arrayNode('image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    'show.html'  => 'EkynaCmsBundle:Admin/Image:show.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Image')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\ImageType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\ImageType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                        ->arrayNode('menu')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    '_form.html'     => 'EkynaCmsBundle:Admin/Menu:_form.html',
                                    'list.html'      => 'EkynaCmsBundle:Admin/Menu:list.html',
                                    'new.html'       => 'EkynaCmsBundle:Admin/Menu:new.html',
                                    'new_child.html' => 'EkynaCmsBundle:Admin/Menu:new_child.html',
                                    'show.html'      => 'EkynaCmsBundle:Admin/Menu:show.html',
                                    'edit.html'      => 'EkynaCmsBundle:Admin/Menu:edit.html',
                                    'remove.html'    => 'EkynaCmsBundle:Admin/Menu:remove.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Menu')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\MenuController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\MenuRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\MenuType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\MenuType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultNull()->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Tag')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\TagType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\TagType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
