<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

use Ekyna\Bundle\CmsBundle\Editor\EditorAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\EditorAwareTrait;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class ViewBuilder
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ViewBuilder implements EditorAwareInterface
{
    use EditorAwareTrait;


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
        $attributes = $view->getAttributes();

        if ($this->editor->isEnabled()) {
            $attributes->set('id', 'cms-content-' . $content->getId());
            $attributes->set('data', [
                'id'      => $content->getId(),
                'actions' => [
                    // TODO
                ],
            ]);
        }

        // Layout
        $this->editor->getLayoutAdapter()->buildContent($content, $view);

        foreach ($content->getContainers() as $container) {
            $view->containers[] = $this->buildContainer($container);
        }

        $attributes->set('classes', trim(
                $attributes->get('classes', '') . ' cms-content')
        );

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
        $attributes = $view->getAttributes();
        $innerAttributes = $view->getInnerAttributes();

        if ($this->editor->isEnabled()) {
            // Container
            $attributes->set('id', 'cms-container-' . $container->getId());
            $attributes->set('data', [
                'id'       => $container->getId(),
                'position' => $container->getPosition(),
                'type'     => $container->getType(),
                'actions'  => [
                    // TODO
                ],
            ]);

            // Inner container
            $innerAttributes->set('id', 'cms-inner-container-' . $container->getId());
        }

        // Layout
        $this->editor->getLayoutAdapter()->buildContainer($container, $view);

        // Plugin
        $this->editor->getContainerPlugin($container->getType())->render($container, $view);

        // Don't build rows if the plugin did generate a content
        if (0 == strlen($view->content)) {
            foreach ($container->getRows() as $row) {
                $view->rows[] = $this->buildRow($row);
            }
        }

        // Container
        $attributes->set('classes', trim(
            $attributes->get('classes', '') . ' cms-container'
        ));

        // Inner container
        $innerAttributes->set('classes', trim(
            $innerAttributes->get('classes', '') . ' cms-inner-container'
        ));

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
        $attributes = $view->getAttributes();

        if ($this->editor->isEnabled()) {
            // Row
            $attributes->set('id', 'cms-row-' . $row->getId());
            $attributes->set('data', [
                'id'       => $row->getId(),
                'position' => $row->getPosition(),
                'actions'  => [
                    // TODO
                ],
            ]);
        }

        // Layout
        $this->editor->getLayoutAdapter()->buildRow($row, $view);

        foreach ($row->getBlocks() as $block) {
            $view->blocks[] = $this->buildBlock($block);
        }

        // Row
        $attributes->set('classes', trim(
            $attributes->get('classes', '') . ' cms-row'
        ));

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
        $attributes = $view->getAttributes();
        $pluginAttributes = $view->getPluginAttributes();

        if ($this->editor->isEnabled()) {
            // Column
            $attributes->set('id', 'cms-column-' . $block->getId());
            $attributes->set('data', [
                'id'       => $block->getId(),
                'position' => $block->getPosition(),
                'actions'  => [
                    'move_left'    => 0 < $block->getPosition(),
                    'move_right'   => true, // TODO is not last
                    'move_up'      => false, // TODO
                    'move_down'    => false, // TODO
                    'offset_left'  => true,
                    'offset_right' => true,
                    'push'         => true,
                    'pull'         => true,
                    'expand'       => true,
                    'compress'     => true,
                    'add'          => true,
                    'remove'       => true,
                ],
            ]);

            // Block
            $pluginAttributes->set('id', 'cms-block-' . $block->getId());
            $pluginAttributes->set('data', [
                'id'      => $block->getId(),
                'type'    => $block->getType(),
                'actions' => [
                    'type' => true,
                    'edit' => true,
                ],
            ]);
        }

        // Layout
        $this->editor->getLayoutAdapter()->buildBlock($block, $view);

        // Plugin
        $this->editor->getBlockPlugin($block->getType())->render($block, $view);

        // Column
        $attributes->set('classes', trim(
            $attributes->get('classes', '') . ' cms-column'
        ));

        // Block
        $pluginAttributes->set('classes', trim(
            $pluginAttributes->get('classes', '') . ' cms-block'
        ));

        return $view;
    }
}
