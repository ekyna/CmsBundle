<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Adapter\Bootstrap3Adapter;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Bundle\CmsBundle\Editor\Plugin;
use Ekyna\Bundle\CmsBundle\Editor\Repository\RepositoryInterface;
use Ekyna\Bundle\CmsBundle\Model as CM;
use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;

/**
 * Class Editor
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Editor
{
    public const URL_PARAMETER = 'cms-editor-enable'; // Used in Typescript files

    private const VIEWPORT_PHONE   = 'phone';
    private const VIEWPORT_TABLET  = 'tablet';
    private const VIEWPORT_LAPTOP  = 'laptop';
    private const VIEWPORT_DESKTOP = 'desktop';
    private const VIEWPORT_ADJUST  = 'adjust';

    private array $config;

    private bool $enabled       = false;
    private int  $viewportWidth = 0;

    private ?AdapterInterface         $layoutAdapter    = null;
    private ?Manager\BlockManager     $blockManager     = null;
    private ?Manager\RowManager       $rowManager       = null;
    private ?Manager\ContainerManager $containerManager = null;
    private ?Manager\ContentManager   $contentManager   = null;
    private ?View\ViewBuilder         $viewBuilder      = null;

    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly Plugin\PluginRegistry $pluginRegistry,
        private readonly LocaleProviderInterface $contentLocaleProvider,
        private readonly PageHelper $pageHelper,
        array   $config
    ) {
        $this->config = array_replace(static::getDefaultConfig(), $config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getBlockManager(): Manager\BlockManager
    {
        if (null !== $this->blockManager) {
            return $this->blockManager;
        }

        $this->blockManager = new Manager\BlockManager(
            $this->config['default_block_plugin']
        );

        $this->blockManager->setEditor($this);

        return $this->blockManager;
    }

    public function getRowManager(): Manager\RowManager
    {
        if (null !== $this->rowManager) {
            return $this->rowManager;
        }

        $this->rowManager = new Manager\RowManager();
        $this->rowManager->setEditor($this);

        return $this->rowManager;
    }

    public function getContainerManager(): Manager\ContainerManager
    {
        if (null !== $this->containerManager) {
            return $this->containerManager;
        }

        $this->containerManager = new Manager\ContainerManager(
            $this->config['default_container_plugin']
        );

        $this->containerManager->setEditor($this);

        return $this->containerManager;
    }

    public function getContentManager(): Manager\ContentManager
    {
        if (null !== $this->contentManager) {
            return $this->contentManager;
        }

        $this->contentManager = new Manager\ContentManager();
        $this->contentManager->setEditor($this);

        return $this->contentManager;
    }

    public function getLayoutAdapter(): AdapterInterface
    {
        if (null !== $this->layoutAdapter) {
            return $this->layoutAdapter;
        }

        $class = $this->config['layout']['adapter'];

        $this->layoutAdapter = new $class();
        $this->layoutAdapter->setEditor($this);

        return $this->layoutAdapter;
    }

    public function getViewBuilder(): View\ViewBuilder
    {
        if (null !== $this->viewBuilder) {
            return $this->viewBuilder;
        }

        $this->viewBuilder = new View\ViewBuilder();
        $this->viewBuilder->setEditor($this);

        return $this->viewBuilder;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setViewportWidth(int $width): void
    {
        $this->viewportWidth = $width;
    }

    public function getViewportWidth(): int
    {
        return $this->viewportWidth;
    }

    /**
     * Returns the block plugin by name.
     */
    public function getBlockPlugin(string $name): Plugin\Block\PluginInterface
    {
        return $this->pluginRegistry->getBlockPlugin($name);
    }

    /**
     * Returns the container plugin by name.
     */
    public function getContainerPlugin(string $name): Plugin\Container\PluginInterface
    {
        return $this->pluginRegistry->getContainerPlugin($name);
    }

    /**
     * Returns the content data.
     */
    public function getContentData(): array
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
     */
    public function createDefaultContent(CM\ContentSubjectInterface|string $subjectOrName): EM\ContentInterface
    {
        return $this->getContentManager()->create($subjectOrName);
    }

    /**
     * Creates a default container.
     *
     * @throws Exception\InvalidOperationException
     */
    public function createDefaultContainer(
        string              $type = null,
        array               $data = [],
        EM\ContentInterface $content = null
    ): EM\ContainerInterface {
        return $this->getContainerManager()->create($content, $type, $data);
    }

    /**
     * Creates a default row.
     *
     * @throws Exception\InvalidOperationException
     */
    public function createDefaultRow(array $data = [], EM\ContainerInterface $container = null): EM\RowInterface
    {
        return $this->getRowManager()->create($container, $data);
    }

    /**
     * Creates a default block.
     *
     * @throws Exception\InvalidOperationException
     */
    public function createDefaultBlock(
        string          $type = null,
        array           $data = [],
        EM\RowInterface $row = null
    ): EM\BlockInterface {
        return $this->getBlockManager()->create($row, $type, $data);
    }

    /**
     * Returns the plugins configuration.
     */
    public function getPluginsConfig(): array
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
     */
    private static function getDefaultConfig(): array
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
     */
    public static function getDefaultViewportsConfig(): array
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
                'title'  => 'Desktop',
            ],
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
     */
    public static function getViewportsKeys(): array
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
     */
    public static function getDefaultLayoutConfig(): array
    {
        return [
            'adapter' => Bootstrap3Adapter::class,
        ];
    }
}
