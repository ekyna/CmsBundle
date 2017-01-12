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
        $attributes = $view->getAttributes()->addClass('cms-content');

        if ($this->editor->isEnabled()) {
            $attributes
                ->setId('cms-content-' . $content->getId())
                ->setData([
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
        $attributes = $view->getAttributes()->addClass('cms-container');
        $innerAttributes = $view->getInnerAttributes()->addClass('cms-inner-container');

        if ($this->editor->isEnabled()) {
            // Container
            $attributes
                ->setId('cms-container-' . $container->getId())
                ->setData([
                    'id'       => $container->getId(),
                    'position' => $container->getPosition(),
                    'type'     => $container->getType(),
                    'actions'  => [
                        // TODO
                    ],
                ]);

            // Inner container
            $innerAttributes->setId('cms-inner-container-' . $container->getId());
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
        $attributes = $view->getAttributes()->addClass('cms-row');

        if ($this->editor->isEnabled()) {
            $attributes
                ->setId('cms-row-' . $row->getId())
                ->setData([
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
        $editable = $this->editor->isEnabled();

        $view = new BlockView();
        $attributes = $view->getAttributes()->addClass('cms-block');

        if ($editable) {
            // Column
            $attributes
                ->setId('cms-block-' . $block->getId())
                ->setData([
                    'id'       => $block->getId(),
                    'type'     => $block->getType(),
                    'position' => $block->getPosition(),
                    'actions'  => [
                        'edit'         => true,
                        'change_type'  => true,
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
        }

        // Layout
        $this->editor
            ->getLayoutAdapter()
            ->buildBlock($block, $view);

        // Plugin
        $this->editor
            ->getBlockPlugin($block->getType())
            ->render($block, $view, [
                'editable' => $this->editor->isEnabled(),
            ]);

        // Prevent type change on a named block
        if (0 < strlen($block->getName())) {
            $attributes->setData('actions', ['change_type' => false]);
            foreach ($view->widgets as $widgetView){
                $widgetView->getAttributes()->setData('actions', ['change_type' => false]);
            }
        }

        return $view;
    }
}
