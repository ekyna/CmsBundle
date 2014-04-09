<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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
                ->booleanNode('enable_contents')->defaultFalse()->end()
                ->arrayNode('defaults')
                    ->isRequired()
                    ->children()
                        ->scalarNode('home_route')->end()
                        ->scalarNode('template')->end()
                        ->scalarNode('controller')->end()
                    ->end()
                ->end()
                ->arrayNode('layouts')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->arrayNode('blocks')
                                ->requiresAtLeastOneElement()
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('type')->end()
                                        ->integerNode('width')->end()
                                    ->end()
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
                                ->scalarNode('templates')->defaultValue('EkynaCmsBundle:Page/Admin')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Page')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\PageController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\PageRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\PageType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\PageType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
