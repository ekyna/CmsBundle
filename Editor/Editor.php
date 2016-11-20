<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\CmsBundle\Editor\Plugin;
use Ekyna\Bundle\CmsBundle\Entity;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Model;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Editor
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Editor
{
//    const CLASS_CONTENT   = 'content';
//    const CLASS_CONTAINER = 'container';
//    const CLASS_ROW       = 'row';
//    const CLASS_BLOCK     = 'block';

    const VIEWPORT_PHONE   = 'phone';
    const VIEWPORT_TABLET  = 'tablet';
    const VIEWPORT_LAPTOP  = 'laptop';
    const VIEWPORT_DESKTOP = 'desktop';
    const VIEWPORT_ADJUST  = 'adjust';


    /**
     * @var Plugin\PluginRegistry
     */
    private $pluginRegistry;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LocaleProviderInterface
     */
    private $contentLocaleProvider;

    /**
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * @var array
     */
    private $config;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var Manager\BlockManager
     */
    private $blockManager;

    /**
     * @var Manager\RowManager
     */
    private $rowManager;

    /**
     * @var Manager\ContainerManager
     */
    private $containerManager;

    /**
     * @var Manager\ContentManager
     */
    private $contentManager;

    /**
     * @var View\ViewBuilder
     */
    private $viewBuilder;


    /**
     * Constructor.
     *
     * @param Plugin\PluginRegistry   $pluginRegistry
     * @param ValidatorInterface      $validator
     * @param LocaleProviderInterface $contentLocaleProvider
     * @param PageHelper              $pageHelper
     * @param array                   $config
     */
    public function __construct(
        Plugin\PluginRegistry $pluginRegistry,
        ValidatorInterface $validator,
        LocaleProviderInterface $contentLocaleProvider,
        PageHelper $pageHelper,
        array $config
    ) {
        $this->pluginRegistry = $pluginRegistry;
        $this->validator = $validator;
        $this->contentLocaleProvider = $contentLocaleProvider;
        $this->pageHelper = $pageHelper;

        $this->config = array_replace(static::getDefaultConfig(), $config);
    }

    /**
     * Returns the config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the block manager.
     *
     * @return Manager\BlockManager
     */
    public function getBlockManager()
    {
        if (null === $this->blockManager) {
            $this->blockManager = new Manager\BlockManager(
                $this,
                $this->pluginRegistry,
                $this->config['default_block_plugin']
            );
        }

        return $this->blockManager;
    }

    /**
     * Returns the row manager.
     *
     * @return Manager\RowManager
     */
    public function getRowManager()
    {
        if (null === $this->rowManager) {
            $this->rowManager = new Manager\RowManager($this);
        }

        return $this->rowManager;
    }

    /**
     * Returns the container manager.
     *
     * @return Manager\ContainerManager
     */
    public function getContainerManager()
    {
        if (null === $this->containerManager) {
            $this->containerManager = new Manager\ContainerManager(
                $this,
                $this->pluginRegistry,
                $this->config['default_container_plugin']
            );
        }

        return $this->containerManager;
    }

    /**
     * Returns the content manager.
     *
     * @return Manager\ContentManager
     */
    public function getContentManager()
    {
        if (null === $this->contentManager) {
            $this->contentManager = new Manager\ContentManager($this);
        }

        return $this->contentManager;
    }

    /**
     * Returns the view builder.
     *
     * @return View\ViewBuilder
     */
    public function getViewBuilder()
    {
        if (null === $this->viewBuilder) {
            $adapterClass = $this->config['layout']['adapter'];
            $this->viewBuilder = new View\ViewBuilder($this, new $adapterClass());
        }

        return $this->viewBuilder;
    }

    /**
     * Sets the enabled.
     *
     * @param bool $enabled
     *
     * @return Editor
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;

        return $this;
    }

    /**
     * Returns the enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the block plugin by name.
     *
     * @param string $name
     *
     * @return Plugin\Block\PluginInterface
     */
    public function getBlockPlugin($name)
    {
        return $this->pluginRegistry->getBlockPlugin($name);
    }

    /**
     * Returns the container plugin by name.
     *
     * @param string $name
     *
     * @return Plugin\Container\PluginInterface
     */
    public function getContainerPlugin($name)
    {
        return $this->pluginRegistry->getContainerPlugin($name);
    }

    /**
     * Returns the content locale.
     *
     * @return string
     */
    public function getContentData()
    {
        $data = [
            'locale' => $this->contentLocaleProvider->getCurrentLocale(),
        ];
        if (null !== $page = $this->pageHelper->getCurrent()) {
            $data['id'] = $page->getId();
        }

        return $data;
    }

    /**
     * Creates a default content for the given subject.
     *
     * @param string|Model\ContentSubjectInterface $subjectOrName
     *
     * @return Model\ContentInterface
     */
    public function createDefaultContent($subjectOrName)
    {
        return $this->getContentManager()->create($subjectOrName);
    }

    /**
     * Creates a default container.
     *
     * @param string                 $type
     * @param array                  $data
     * @param Model\ContentInterface $content
     *
     * @return Model\ContainerInterface
     */
    public function createDefaultContainer($type = null, array $data = [], Model\ContentInterface $content = null)
    {
        return $this->getContainerManager()->create($content, $type, $data);
    }

    /**
     * Creates a default row.
     *
     * @param array                    $data
     * @param Model\ContainerInterface $container
     *
     * @return Model\RowInterface
     */
    public function createDefaultRow(array $data = [], Model\ContainerInterface $container = null)
    {
        return $this->getRowManager()->create($container, $data);
    }

    /**
     * Creates a default block.
     *
     * @param string             $type
     * @param array              $data
     * @param Model\RowInterface $row
     *
     * @return Model\BlockInterface
     */
    public function createDefaultBlock($type = null, array $data = [], Model\RowInterface $row = null)
    {
        return $this->getBlockManager()->create($row, $type, $data);
    }

    /**
     * Returns the plugins configuration.
     *
     * @return array
     */
    public function getPluginsConfig()
    {
        $config = [
            'block'     => [],
            'container' => [],
        ];
        foreach ($this->pluginRegistry->getBlockPlugins() as $plugin) {
            $config['block'][] = [
                'name'  => $plugin->getName(),
                'title' => $plugin->getTitle(),
                'path'  => $plugin->getJavascriptFilePath(),
            ];
        }
        foreach ($this->pluginRegistry->getContainerPlugins() as $plugin) {
            $config['container'][] = [
                'name'  => $plugin->getName(),
                'title' => $plugin->getTitle(),
                'path'  => $plugin->getJavascriptFilePath(),
            ];
        }

        return $config;
    }

    /**
     * Returns the default editor configuration.
     *
     * @return array
     */
    static private function getDefaultConfig()
    {
        return [
            'locales'                  => ['en'],
            //'classes'                  => static::getDefaultClassesConfig(),
            'viewports'                => static::getDefaultViewportsConfig(),
            'layout'                   => static::getDefaultLayoutConfig(),
            'block_min_size'           => 2,
            'default_block_plugin'     => 'ekyna_block_tinymce',
            'default_container_plugin' => 'ekyna_container_background',
        ];
    }

    /**
     * Returns the default viewports configuration.
     *
     * @return array
     */
    /*static function getDefaultClasses()
    {
        return [
            'content'   => Entity\Content::class,
            'container' => Entity\Container::class,
            'row'       => Entity\Row::class,
            'block'     => Entity\Block::class,
        ];
    }*/

    /**
     * Returns the default viewports configuration.
     *
     * @return array
     */
    /*static function getClassesKeys()
    {
        return [
            static::CLASS_CONTENT,
            static::CLASS_CONTAINER,
            static::CLASS_ROW,
            static::CLASS_BLOCK,
        ];
    }*/

    /**
     * Returns the default viewports configuration.
     *
     * @return array
     */
    static function getDefaultViewportsConfig()
    {
        return [
            [
                'name'   => static::VIEWPORT_PHONE,
                'width'  => 320,
                'height' => 568,
                'icon'   => 'mobile',
                'title'  => 'Smartphone',
            ],
            [
                'name'   => static::VIEWPORT_TABLET,
                'width'  => 768,
                'height' => 1024,
                'icon'   => 'tablet',
                'title'  => 'Tablet',
            ],
            [
                'name'   => static::VIEWPORT_LAPTOP,
                'width'  => 1280,
                'height' => 800,
                'icon'   => 'laptop',
                'title'  => 'Laptop',
            ],
            [
                'name'   => static::VIEWPORT_DESKTOP,
                'width'  => 1920,
                'height' => 1080,
                'icon'   => 'desktop',
                'title'  => 'Desktop'],
            [
                'name'   => static::VIEWPORT_ADJUST,
                'icon'   => 'arrows-alt',
                'title'  => 'Adjust to screen',
                'active' => true,
            ],
        ];
    }

    /**
     * Returns the default viewports configuration.
     *
     * @return array
     */
    static function getViewportsKeys()
    {
        return [
            static::VIEWPORT_PHONE,
            static::VIEWPORT_TABLET,
            static::VIEWPORT_LAPTOP,
            static::VIEWPORT_DESKTOP,
            static::VIEWPORT_ADJUST,
        ];
    }

    /**
     * Returns the default layout configuration.
     *
     * @return array
     */
    static function getDefaultLayoutConfig()
    {
        return [
            'adapter' => Bootstrap3Adapter::class,
        ];
    }
}
