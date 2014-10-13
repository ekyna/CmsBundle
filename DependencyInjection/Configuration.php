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
                ->scalarNode('output_dir')->defaultValue('')->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('home_route')->defaultValue('home')->end()
                        ->scalarNode('template')->defaultValue('EkynaCmsBundle:Cms:default.html.twig')->end()
                        ->scalarNode('controller')->defaultValue('EkynaCmsBundle:Cms:default')->end()
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
                                    '_form.html'     => 'EkynaCmsBundle:Page/Admin:_form.html',
                                    'list.html'      => 'EkynaCmsBundle:Page/Admin:list.html',
                                    'new.html'       => 'EkynaCmsBundle:Page/Admin:new.html',
                                    'new_child.html' => 'EkynaCmsBundle:Page/Admin:new_child.html',
                                    'show.html'      => 'EkynaCmsBundle:Page/Admin:show.html',
                                    'edit.html'      => 'EkynaCmsBundle:Page/Admin:edit.html',
                                    'remove.html'    => 'EkynaCmsBundle:Page/Admin:remove.html',
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
                        ->arrayNode('image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    'show.html'  => 'EkynaCmsBundle:Image/Admin:show.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Image')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\ImageType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\ImageType')->end()
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
