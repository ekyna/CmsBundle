<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

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
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_cms');

        $rootNode
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

        $this->addSeoSection($rootNode);
        $this->addPageSection($rootNode);
        $this->addMenuSection($rootNode);
        $this->addEditorSection($rootNode);
        $this->addSlideShowSection($rootNode);
        $this->addSchemaOrgSection($rootNode);
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
     * Adds `slide show` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addSlideShowSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
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
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('js_path')->isRequired()->cannotBeEmpty()->end()
                                    ->scalarNode('label')->isRequired()->cannotBeEmpty()->end()
                                    ->variableNode('config')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Adds `schema.org` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addSchemaOrgSection(ArrayNodeDefinition $node)
    {
        /** @noinspection PhpUndefinedMethodInspection */
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
                        ->scalarNode('template')->defaultValue('@EkynaCms/Editor/content.html.twig')->end()
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
                                            ->defaultValue('ekyna_block_tinymce')
                                        ->end()
                                        ->integerNode('min_size')
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
                                        ->scalarNode('default')->defaultValue('ekyna_container_background')->end()
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
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Tag')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\TagType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\TagType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    '_form.html'     => '@EkynaCms/Admin/Page/_form.html',
                                    'list.html'      => '@EkynaCms/Admin/Page/list.html',
                                    'new.html'       => '@EkynaCms/Admin/Page/new.html',
                                    'new_child.html' => '@EkynaCms/Admin/Page/new_child.html',
                                    'show.html'      => '@EkynaCms/Admin/Page/show.html',
                                    'edit.html'      => '@EkynaCms/Admin/Page/edit.html',
                                    'remove.html'    => '@EkynaCms/Admin/Page/remove.html',
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
                                    '_form.html'     => '@EkynaCms/Admin/Menu/_form.html',
                                    'list.html'      => '@EkynaCms/Admin/Menu/list.html',
                                    'new.html'       => '@EkynaCms/Admin/Menu/new.html',
                                    'new_child.html' => '@EkynaCms/Admin/Menu/new_child.html',
                                    'show.html'      => '@EkynaCms/Admin/Menu/show.html',
                                    'edit.html'      => '@EkynaCms/Admin/Menu/edit.html',
                                    'remove.html'    => '@EkynaCms/Admin/Menu/remove.html',
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
                        ->arrayNode('slide_show')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    '_form.html' => '@EkynaCms/Admin/SlideShow/_form.html',
                                    'show.html'  => '@EkynaCms/Admin/SlideShow/show.html',
                                ])->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\SlideShow')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\SlideShowController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\SlideShowType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\SlideShowType')->end()
                                ->scalarNode('parent')->end()
                                ->scalarNode('event')->end()
                                /*->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\MenuTranslation')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['title', 'path'])
                                        ->end()
                                    ->end()
                                ->end()*/
                            ->end()
                        ->end()
                        ->arrayNode('slide')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    '_form.html'  => '@EkynaCms/Admin/Slide/_form.html',
                                    'show.html'   => '@EkynaCms/Admin/Slide/show.html',
                                    'new.html'    => '@EkynaCms/Admin/Slide/new.html',
                                    'edit.html'   => '@EkynaCms/Admin/Slide/edit.html',
                                    'remove.html' => '@EkynaCms/Admin/Slide/remove.html',
                                ])->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\Slide')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\CmsBundle\Controller\Admin\SlideController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\CmsBundle\Form\Type\SlideType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\CmsBundle\Table\Type\SlideType')->end()
                                ->scalarNode('parent')->defaultValue('ekyna_cms.slide_show')->end()
                                ->scalarNode('event')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\CmsBundle\Entity\SlideTranslation')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['data'])
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
