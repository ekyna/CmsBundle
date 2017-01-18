<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\CmsBundle\Editor\Repository\RepositoryInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Bundle\CmsBundle\Editor\Plugin;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Model as CM;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Editor
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Editor
{
    const VIEWPORT_PHONE   = 'phone';
    const VIEWPORT_TABLET  = 'tablet';
    const VIEWPORT_LAPTOP  = 'laptop';
    const VIEWPORT_DESKTOP = 'desktop';
    const VIEWPORT_ADJUST  = 'adjust';


    /**
     * @var RepositoryInterface
     */
    private $repository;

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
     * @var int
     */
    private $viewportWidth;

    /**
     * @var AdapterInterface
     */
    private $layoutAdapter;

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
     * @param RepositoryInterface     $factory
     * @param Plugin\PluginRegistry   $pluginRegistry
     * @param ValidatorInterface      $validator
     * @param LocaleProviderInterface $contentLocaleProvider
     * @param PageHelper              $pageHelper
     * @param array                   $config
     */
    public function __construct(
        RepositoryInterface $factory,
        Plugin\PluginRegistry $pluginRegistry,
        ValidatorInterface $validator,
        LocaleProviderInterface $contentLocaleProvider,
        PageHelper $pageHelper,
        array $config
    ) {
        $this->repository = $factory;
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
     * Returns the factory.
     *
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
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
                $this->config['default_block_plugin']
            );
            $this->blockManager->setEditor($this);
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
            $this->rowManager->setEditor($this);
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
                $this->config['default_container_plugin']
            );
            $this->containerManager->setEditor($this);
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
            $this->contentManager->setEditor($this);
        }

        return $this->contentManager;
    }

    /**
     * Returns the layout adapter.
     *
     * @return AdapterInterface
     */
    public function getLayoutAdapter()
    {
        if (null === $this->layoutAdapter) {
            $class = $this->config['layout']['adapter'];

            $this->layoutAdapter = new $class;
            $this->layoutAdapter->setEditor($this);
        }

        return $this->layoutAdapter;
    }

    /**
     * Returns the view builder.
     *
     * @return View\ViewBuilder
     */
    public function getViewBuilder()
    {
        if (null === $this->viewBuilder) {
            $this->viewBuilder = new View\ViewBuilder();
            $this->viewBuilder->setEditor($this);
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
        return (bool)$this->enabled;
    }

    /**
     * Returns the viewport width.
     *
     * @return int
     */
    public function getViewportWidth()
    {
        return $this->viewportWidth;
    }

    /**
     * Sets the viewport width.
     *
     * @param int $width
     */
    public function setViewportWidth($width)
    {
        $this->viewportWidth = $width;
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
     * @param string|CM\ContentSubjectInterface $subjectOrName
     *
     * @return EM\ContentInterface
     */
    public function createDefaultContent($subjectOrName)
    {
        return $this->getContentManager()->create($subjectOrName);
    }

    /**
     * Creates a default container.
     *
     * @param string              $type
     * @param array               $data
     * @param EM\ContentInterface $content
     *
     * @return EM\ContainerInterface
     */
    public function createDefaultContainer($type = null, array $data = [], EM\ContentInterface $content = null)
    {
        return $this->getContainerManager()->create($content, $type, $data);
    }

    /**
     * Creates a default row.
     *
     * @param array                 $data
     * @param EM\ContainerInterface $container
     *
     * @return EM\RowInterface
     */
    public function createDefaultRow(array $data = [], EM\ContainerInterface $container = null)
    {
        return $this->getRowManager()->create($container, $data);
    }

    /**
     * Creates a default block.
     *
     * @param string          $type
     * @param array           $data
     * @param EM\RowInterface $row
     *
     * @return EM\BlockInterface
     */
    public function createDefaultBlock($type = null, array $data = [], EM\RowInterface $row = null)
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
    static function getDefaultViewportsConfig()
    {
        return [
            [
                'name'   => static::VIEWPORT_PHONE,
                'width'  => 320,
                'height' => 568,
                'icon'   => 'smartphone',
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
                'icon'   => 'fullscreen',
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
