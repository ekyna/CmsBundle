<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class ViewBuilder
 * @package Ekyna\Bundle\CmsBundle\Editor
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
     * @param Editor   $editor
     * @param AdapterInterface $layoutAdapter
     */
    public function __construct(Editor $editor, AdapterInterface $layoutAdapter)
    {
        $this->editor        = $editor;
        $this->layoutAdapter = $layoutAdapter;
    }

    /**
     * Builds the content view.
     *
     * @param Model\ContentInterface $content
     * @return View\Content
     */
    public function buildContent(Model\ContentInterface $content)
    {
        $view = new View\Content();

        $this->layoutAdapter->buildContent($content, $view);

        if ($this->editor->isEnabled()) {
            $view->attributes['id'] = 'cms-content-' . $content->getId();
            $view->attributes['data-cms-content'] = json_encode([
                'id' => $content->getId()
            ]);
            $class = array_key_exists('class', $view->attributes) ? $view->attributes['class'] : '';
            $view->attributes['class'] = trim($class . ' cms-content');
        }

        foreach ($content->getContainers() as $container) {
            $view->containers[] = $this->buildContainer($container);
        }

        return $view;
    }

    /**
     * Builds the container view.
     *
     * @param Model\ContainerInterface $container
     * @return View\Container
     */
    public function buildContainer(Model\ContainerInterface $container)
    {
        $view = new View\Container();

        $this->layoutAdapter->buildContainer($container, $view);

        if ($this->editor->isEnabled()) {
            $view->attributes['id'] = 'cms-container-' . $container->getId();
            $view->attributes['data-cms-container'] = json_encode([
                'id'       => $container->getId(),
                'position' => $container->getPosition(),
            ]);
            $class = array_key_exists('class', $view->attributes) ? $view->attributes['class'] : '';
            $view->attributes['class'] = trim($class . ' cms-container');
        }

        $size = 0;
        $row = new View\Row();
        foreach ($container->getBlocks() as $block) {
            $row->blocks[] = $this->buildBlock($block);

            $size += $block->getSize();
            if ($size % 12 == 0) {
                $this->layoutAdapter->buildRow($block, $row);

                if ($this->editor->isEnabled()) {
                    $row->attributes['data-cms-row'] = json_encode([
                        'position' => $block->getRow(),
                    ]);
                    $class = array_key_exists('class', $row->attributes) ? $row->attributes['class'] : '';
                    $row->attributes['class'] = trim($class . ' cms-row');
                }

                $view->rows[] = $row;
                $row = new View\Row();
            }
        }

        return $view;
    }

    /**
     * Builds the block view.
     *
     * @param Model\BlockInterface $block
     * @return View\Block
     */
    public function buildBlock(Model\BlockInterface $block)
    {
        $view = new View\Block();

        $this->layoutAdapter->buildBlock($block, $view);

        if ($this->editor->isEnabled()) {
            // Column
            $view->columnAttributes['id'] = 'cms-column-' . $block->getId();
            $view->columnAttributes['data-cms-column'] = json_encode([
                'id'       => $block->getId(),
                'position' => $block->getColumn(),
                'size'     => $block->getSize(),
            ]);
            $class = array_key_exists('class', $view->columnAttributes) ? $view->columnAttributes['class'] : '';
            $view->columnAttributes['class'] = trim($class . ' cms-column');

            // Block
            $view->blockAttributes['id'] = 'cms-block-' . $block->getId();
            $view->blockAttributes['data-cms-block'] = json_encode([
                'id'   => $block->getId(),
                'type' => $block->getType()
            ]);
            $class = array_key_exists('class', $view->blockAttributes) ? $view->blockAttributes['class'] : '';
            $view->blockAttributes['class'] = trim($class . ' cms-block');
        }

        $plugin = $this->editor->getPluginByName($block->getType());
        $view->content = $plugin->render($block);

        return $view;
    }
}
