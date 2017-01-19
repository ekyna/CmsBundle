<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\FeaturePlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\ImagePlugin;
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
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_cms');

        /** @noinspection PhpUndefinedMethodInspection */
        $rootNode
            ->children()
                ->scalarNode('output_dir')->defaultValue('')->cannotBeEmpty()->end()
                ->booleanNode('esi_flashes')->defaultFalse()->end()
                ->scalarNode('home_route')->defaultNull()->end()
            ->end()
        ;

        $this->addSeoSection($rootNode);
        $this->addPageSection($rootNode);
        $this->addMenuSection($rootNode);
        $this->addEditorSection($rootNode);
        $this->addPoolsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `seo` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addSeoSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('seo')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('no_follow')->defaultFalse()->end()
                        ->booleanNode('no_index')->defaultFalse()->end()
                        ->scalarNode('title_append')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `page` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPageSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('page')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('controllers')
                            ->defaultValue(['default' => [
                                'title'    => 'Par défaut',
                                'value'    => 'EkynaCmsBundle:Cms:default',
                                'advanced' => true,
                            ]])
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('value')->isRequired()->cannotBeEmpty()->end()
                                    ->booleanNode('advanced')->defaultFalse()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cookie_consent')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enable')->defaultTrue()->end()
                                ->scalarNode('controller')->defaultValue('EkynaCmsBundle:Cms:default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('wide_search')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enable')->defaultTrue()->end()
                                ->scalarNode('controller')->defaultValue('EkynaCmsBundle:Cms:search')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `menu` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addMenuSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('menu')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('roots')
                            ->defaultValue(['main' => [
                                'title'       => 'Navigation principale',
                                'description' => 'Barre de navigation principale',
                            ]])
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
            ->end();
    }

    /**
     * Adds `editor` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addEditorSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('editor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('EkynaCmsBundle:Editor:content.html.twig')->end()
                        ->scalarNode('css_path')->defaultValue('/bundles/ekynacms/css/editor-document.css')->end()
                        ->arrayNode('viewports')
                            ->defaultValue(Editor::getDefaultViewportsConfig())
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->integerNode('width')->isRequired()->min(0)->defaultValue(0)->end()
                                    ->integerNode('height')->isRequired()->min(0)->defaultValue(0)->end()
                                    ->scalarNode('icon')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                    ->booleanNode('active')->defaultFalse()->end()
                                ->end()
                            ->end()
                            ->validate()
                            ->ifTrue(function($classes) {
                                if (0 < count(array_diff(Editor::getViewportsKeys(), (array) $classes))) {
                                    return true;
                                }
                                return false;
                            })
                            ->thenInvalid('Unexpected key(s) in editor classes configuration.')
                            ->end()
                        ->end()
                        ->arrayNode('layout')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('adapter')
                                    ->defaultValue(Bootstrap3Adapter::class)
                                    ->isRequired()->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('plugins')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('block')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')
                                            ->isRequired()
                                            ->defaultValue('ekyna_block_tinymce')
                                        ->end()
                                        ->integerNode('min_size')
                                            ->isRequired()
                                            ->min(1)->max(12)
                                            ->defaultValue(2)
                                        ->end()
                                        ->arrayNode('tinymce')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default_content')
                                                    ->defaultValue('<p>Default content.</p>')
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('image')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default_path')
                                                    ->defaultValue('/bundles/ekynacms/img/default-image.gif')
                                                ->end()
                                                ->scalarNode('default_alt')
                                                    ->defaultValue('Default image')
                                                ->end()
                                                ->scalarNode('filter')->defaultValue('cms_block_image')->end()
                                                ->arrayNode('styles')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(ImagePlugin::getDefaultStyleChoices())
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('feature')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('image_filter')->defaultValue('cms_block_feature')->end()
                                                ->arrayNode('styles')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(FeaturePlugin::getDefaultStyleChoices())
                                                ->end()
                                                ->arrayNode('animations')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(FeaturePlugin::getDefaultAnimationChoices())
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('container')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('ekyna_container_background')->end()
                                        ->arrayNode('background')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('filter')->defaultValue('cms_container_background')->end()
                                                ->scalarNode('default_color')->defaultValue('')->end()
                                                ->integerNode('default_padding_top')->defaultValue(0)->end()
                                                ->integerNode('default_padding_bottom')->defaultValue(0)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `pools` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPoolsSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $node
            ->children()
                ->arrayNode('pools')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('block')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Editor\Block')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\BlockRepository')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Editor\BlockTranslation')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['data'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('container')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Editor\Container')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\ContainerRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('content')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Editor\Content')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\ContentRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('row')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Editor\Row')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\RowRepository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('seo')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Seo')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\SeoRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\SeoType')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\SeoTranslation')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['title', 'description', 'keywords'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    '_form.html'     => 'EkynaCmsBundle:Admin/Page:_form.html',
                                    'list.html'      => 'EkynaCmsBundle:Admin/Page:list.html',
                                    'new.html'       => 'EkynaCmsBundle:Admin/Page:new.html',
                                    'new_child.html' => 'EkynaCmsBundle:Admin/Page:new_child.html',
                                    'show.html'      => 'EkynaCmsBundle:Admin/Page:show.html',
                                    'edit.html'      => 'EkynaCmsBundle:Admin/Page:edit.html',
                                    'remove.html'    => 'EkynaCmsBundle:Admin/Page:remove.html',
                                ])->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Page')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\PageController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\PageRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\PageType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\PageType')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('event')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\PageTranslation')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['title', 'breadcrumb', 'html', 'path'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('menu')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    '_form.html'     => 'EkynaCmsBundle:Admin/Menu:_form.html',
                                    'list.html'      => 'EkynaCmsBundle:Admin/Menu:list.html',
                                    'new.html'       => 'EkynaCmsBundle:Admin/Menu:new.html',
                                    'new_child.html' => 'EkynaCmsBundle:Admin/Menu:new_child.html',
                                    'show.html'      => 'EkynaCmsBundle:Admin/Menu:show.html',
                                    'edit.html'      => 'EkynaCmsBundle:Admin/Menu:edit.html',
                                    'remove.html'    => 'EkynaCmsBundle:Admin/Menu:remove.html',
                                ])->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Menu')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\MenuController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\MenuRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\MenuType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\MenuType')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('event')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\MenuTranslation')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['title', 'path'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
