<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

use Ekyna\Bundle\CmsBundle\Editor\EditorAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\EditorAwareTrait;
use Ekyna\Bundle\CmsBundle\Editor\Model;

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
        // Rendering source (for copy containers)
        $source = is_null($container->getCopy()) ? $container : $container->getCopy();

        $view = new ContainerView();
        $attributes = $view->getAttributes()->addClass('cms-container');
        $attributes->setId('cms-container-' . $container->getId());

        $innerAttributes = $view->getInnerAttributes()->addClass('cms-inner-container');

        if ($this->editor->isEnabled()) {
            $content = $container->getContent();

            // Container
            $attributes
                ->setData([
                    'id'       => $container->getId(),
                    'position' => $container->getPosition(),
                    'type'     => $container->getType(),
                    'actions'  => [
                        'add'         => !is_null($content),
                        'edit'        => true,
                        'layout'      => is_null($container->getCopy()),
                        'change_type' => !$container->isNamed() && !$container->isTitled(),
                        'move_up'     => !$container->isFirst(),
                        'move_down'   => !$container->isLast(),
                        'remove'      => !$container->isAlone(),
                    ],
                ]);

            // Inner container
            $innerAttributes
                ->setId('cms-inner-container-' . $source->getId())
                ->setData([
                    'id' => $source->getId(),
                ]);
        }

        // Layout
        $this->editor->getLayoutAdapter()->buildContainer($source, $view);
        $view->content = '<span class="cms-container-anchor">&nbsp;</span>' . $view->content;


        // Plugin
        $this->editor->getContainerPlugin($source->getType())->render($source, $view);

        // Don't build rows if the plugin did generate a content
        if (0 == strlen($view->innerContent)) {
            foreach ($source->getRows() as $row) {
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
            $container = $row->getContainer();

            $attributes
                ->setId('cms-row-' . $row->getId())
                ->setData([
                    'id'       => $row->getId(),
                    'position' => $row->getPosition(),
                    'actions'  => [
                        'add'       => !is_null($container),
                        //'edit'         => true,
                        'layout'    => true,
                        //'change_type'  => !$row->isNamed(),
                        'move_up'   => !$row->isFirst(),
                        'move_down' => !$row->isLast(),
                        'remove'    => !$row->isAlone(),
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
            $row = $block->getRow();

            $attributes
                ->setId('cms-block-' . $block->getId())
                ->setData([
                    'id'       => $block->getId(),
                    'type'     => $block->getType(),
                    'position' => $block->getPosition(),
                    'actions'  => [
                        'add'         => !is_null($row),
                        'edit'        => false,
                        'layout'      => true,
                        'change_type' => !$block->isNamed(),
                        'move_left'   => !$block->isFirst(),
                        'move_right'  => !$block->isLast(),
                        'move_up'     => !$block->isAlone() && !$row->isFirst(),
                        'move_down'   => !$block->isAlone() && !$row->isLast(),
                        'remove'      => !$block->isAlone(),
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
                'editable' => $editable,
            ]);

        // Set widgets positions
        /*$p = 0;
        foreach ($view->widgets as $widgetView) {
            $widgetView->getAttributes()->setData('position', $p);
            $p++;
        }*/

        return $view;
    }
}
