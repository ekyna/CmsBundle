<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Ekyna\Bundle\CmsBundle\Controller\CmsController;
use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PropertyDefaults;
use Ekyna\Bundle\CmsBundle\SlideShow\Type\AbstractType;
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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('ekyna_cms');
        $root = $builder->getRootNode();

        $root
            ->children()
                ->scalarNode('home_route')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('public_locales')
                    ->scalarPrototype()->end()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        $this->addSeoSection($root);
        $this->addPageSection($root);
        $this->addMenuSection($root);
        $this->addEditorSection($root);
        $this->addSlideShowSection($root);
        $this->addNoticeSection($root);
        $this->addSchemaOrgSection($root);

        return $builder;
    }

    /**
     * Adds `seo` section.
     */
    private function addSeoSection(ArrayNodeDefinition $node): void
    {
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
     */
    private function addPageSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('page')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('controllers')
                            ->defaultValue(['default' => [
                                'title'    => 'Par défaut',
                                'value'    => CmsController::class . '::page',
                                'advanced' => true,
                            ]])
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('value')->isRequired()->cannotBeEmpty()->end()
                                    ->booleanNode('advanced')->defaultFalse()->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('cookie_consent')
                            ->canBeDisabled()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('controller')->defaultValue(CmsController::class . '::page')->end()
                            ->end()
                        ->end()
                        ->arrayNode('wide_search')
                            ->canBeDisabled()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('controller')->defaultValue(CmsController::class . '::search')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `menu` section.
     */
    private function addMenuSection(ArrayNodeDefinition $node): void
    {
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
                            ->arrayPrototype()
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
     * Adds `slide show` section.
     */
    private function addSlideShowSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('slide_show')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('static')
                            ->defaultValue([])
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('themes')
                            ->defaultValue(AbstractType::getDefaultThemeChoices())
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('types')
                            ->defaultValue([])
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('js_path')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('label')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('domain')->defaultNull()->end()
                                    ->variableNode('config')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `notice` section.
     */
    private function addNoticeSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('notice')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')
                            ->defaultValue('@EkynaCms/Cms/Fragment/notices.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `schema.org` section.
     */
    private function addSchemaOrgSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('schema_org')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('provider')
                            ->defaultValue([])
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `editor` section.
     */
    private function addEditorSection(ArrayNodeDefinition $node): void
    {
        // TODO Split (chain analysis is too long...)
        $node
            ->children()
                ->arrayNode('editor')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('template')->defaultValue('@EkynaCms/Editor/content.html.twig')->end()
                        ->scalarNode('css_path')->defaultValue('/bundles/ekynacms/css/editor-document.css')->end()
                        ->arrayNode('viewports')
                            ->defaultValue(Editor::getDefaultViewportsConfig())
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
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
                                ->arrayNode('default')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('block')
                                            ->defaultValue('ekyna_block_tinymce')
                                        ->end()
                                        ->scalarNode('container')
                                            ->defaultValue('ekyna_container_background')
                                        ->end()
                                        ->integerNode('min_size')
                                            ->min(1)->max(12)
                                            ->defaultValue(2)
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('block')
                                    ->addDefaultsIfNotSet()
                                    ->children()
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
                                                ->arrayNode('themes')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(PropertyDefaults::getDefaultThemeChoices())
                                                ->end()
                                                ->arrayNode('styles')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(PropertyDefaults::getDefaultStyleChoices())
                                                ->end()
                                                ->arrayNode('animations')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(PropertyDefaults::getDefaultAnimationChoices())
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('video')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default_poster')
                                                    ->defaultValue('/bundles/ekynacms/img/default-image.gif')
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('feature')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('image_filter')->defaultValue('cms_block_feature')->end()
                                                ->arrayNode('animations')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(PropertyDefaults::getDefaultAnimationChoices())
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('template')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->arrayNode('templates')
                                                    ->defaultValue([])
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('array')
                                                        ->children()
                                                            ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                                            ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('tabs')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->arrayNode('themes')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(PropertyDefaults::getDefaultThemeChoices())
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('container')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('background')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('filter')->defaultValue('cms_container_background')->end()
                                                ->scalarNode('default_color')->defaultValue('')->end()
                                                ->arrayNode('themes')
                                                    ->useAttributeAsKey('name')
                                                    ->prototype('scalar')->end()
                                                    ->defaultValue(PropertyDefaults::getDefaultThemeChoices())
                                                ->end()
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
}
