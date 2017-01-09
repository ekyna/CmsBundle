<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\EditorAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface AdapterInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface AdapterInterface extends EditorAwareInterface
{
    /**
     * Builds the content view.
     *
     * @param Model\ContentInterface $content
     * @param View\ContentView       $view
     */
    public function buildContent(Model\ContentInterface $content, View\ContentView $view);

    /**
     * Builds the container view.
     *
     * @param Model\ContainerInterface $container
     * @param View\ContainerView       $view
     */
    public function buildContainer(Model\ContainerInterface $container, View\ContainerView $view);

    /**
     * Builds the row view.
     *
     * @param Model\RowInterface $row
     * @param View\RowView       $view
     */
    public function buildRow(Model\RowInterface $row, View\RowView $view);

    /**
     * Builds the column view.
     *
     * @param Model\BlockInterface $block
     * @param View\BlockView       $view
     */
    public function buildBlock(Model\BlockInterface $block, View\BlockView $view);

    /**
     * Expands the block.
     *
     * @param Model\BlockInterface $block
     */
    public function expandBlock(Model\BlockInterface $block);

    /**
     * Compresses the block.
     *
     * @param Model\BlockInterface $block
     */
    public function compressBlock(Model\BlockInterface $block);

    /**
     * Pulls the block.
     *
     * @param Model\BlockInterface $block
     */
    public function pullBlock(Model\BlockInterface $block);

    /**
     * Pushes the block.
     *
     * @param Model\BlockInterface $block
     */
    public function pushBlock(Model\BlockInterface $block);

    /**
     * Offsets the block to the left.
     *
     * @param Model\BlockInterface $block
     */
    public function offsetLeftBlock(Model\BlockInterface $block);

    /**
     * Offsets the block to the right.
     *
     * @param Model\BlockInterface $block
     */
    public function offsetRightBlock(Model\BlockInterface $block);
}
