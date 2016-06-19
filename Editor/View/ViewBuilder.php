<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class ViewBuilder
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ViewBuilder
{
    /**
     * @var Editor
     */
    private $editor;

    /**
     * @var AdapterInterface
     */
    private $layoutAdapter;


    /**
     * Constructor.
     *
     * @param Editor           $editor
     * @param AdapterInterface $layoutAdapter
     */
    public function __construct(Editor $editor, AdapterInterface $layoutAdapter)
    {
        $this->editor = $editor;
        $this->layoutAdapter = $layoutAdapter;
    }

    /**
     * Builds the content view.
     *
     * @param Model\ContentInterface $content
     *
     * @return ContentView
     */
    public function buildContent(Model\ContentInterface $content)
    {
        $view = new ContentView();

        $this->layoutAdapter->buildContent($content, $view);

        foreach ($content->getContainers() as $container) {
            $view->containers[] = $this->buildContainer($container);
        }

        if ($this->editor->isEnabled()) {
            $view->attributes['id'] = 'cms-content-' . $content->getId();
            $view->attributes['data'] = [
                'id' => $content->getId(),
            ];
            $classes = array_key_exists('classes', $view->attributes) ? $view->attributes['classes'] : '';
            $view->attributes['classes'] = trim($classes . ' cms-content');
        }

        return $view;
    }

    /**
     * Builds the container view.
     *
     * @param Model\ContainerInterface $container
     *
     * @return ContainerView
     */
    public function buildContainer(Model\ContainerInterface $container)
    {
        $view = new ContainerView();

        $this->layoutAdapter->buildContainer($container, $view);

        $plugin = $this->editor->getContainerPlugin($container->getType());
        $plugin->render($container, $view);

        // Don't build rows if the plugin did generate a content
        if (0 == strlen($view->content)) {
            foreach ($container->getRows() as $row) {
                $view->rows[] = $this->buildRow($row);
            }
        }

        if ($this->editor->isEnabled()) {
            $view->attributes['id'] = 'cms-container-' . $container->getId();
            $view->attributes['data'] = [
                'id'       => $container->getId(),
                'position' => $container->getPosition(),
                'type'     => $container->getType(),
            ];
            $classes = array_key_exists('classes', $view->attributes) ? $view->attributes['classes'] : '';
            $view->attributes['classes'] = trim($classes . ' cms-container');

            // Inner container
            $view->innerAttributes['id'] = 'cms-inner-container-' . $container->getId();
            $classes = array_key_exists('classes', $view->innerAttributes) ? $view->innerAttributes['classes'] : '';
            $view->innerAttributes['classes'] = trim($classes . ' cms-inner-container');
        }

        return $view;
    }

    /**
     * Builds the row view.
     *
     * @param Model\RowInterface $row
     *
     * @return RowView
     */
    public function buildRow(Model\RowInterface $row)
    {
        $view = new RowView();

        $this->layoutAdapter->buildRow($row, $view);

        foreach ($row->getBlocks() as $block) {
            $view->blocks[] = $this->buildBlock($block);
        }

        if ($this->editor->isEnabled()) {
            $view->attributes['id'] = 'cms-row-' . $row->getId();
            $view->attributes['data'] = [
                'id'       => $row->getId(),
                'position' => $row->getPosition(),
            ];
            $classes = array_key_exists('classes', $view->attributes) ? $view->attributes['classes'] : '';
            $view->attributes['classes'] = trim($classes . ' cms-row');
        }

        return $view;
    }

    /**
     * Builds the block view.
     *
     * @param Model\BlockInterface $block
     *
     * @return BlockView
     */
    public function buildBlock(Model\BlockInterface $block)
    {
        $view = new BlockView();

        $this->layoutAdapter->buildBlock($block, $view);

        $plugin = $this->editor->getBlockPlugin($block->getType());
        $plugin->render($block, $view);

        if ($this->editor->isEnabled()) {
            // Column
            $view->attributes['id'] = 'cms-column-' . $block->getId();
            $view->attributes['data'] = [
                'id'       => $block->getId(),
                'position' => $block->getPosition(),
                'size'     => $block->getSize(),
            ];
            $classes = array_key_exists('classes', $view->attributes) ? $view->attributes['classes'] : '';
            $view->attributes['classes'] = trim($classes . ' cms-column');

            // Block
            $view->pluginAttributes['id'] = 'cms-block-' . $block->getId();
            $view->pluginAttributes['data'] = [
                'id'   => $block->getId(),
                'type' => $block->getType(),
            ];
            $classes = array_key_exists('classes', $view->pluginAttributes) ? $view->pluginAttributes['classes'] : '';
            $view->pluginAttributes['classes'] = trim($classes . ' cms-block');
        }

        return $view;
    }
}
