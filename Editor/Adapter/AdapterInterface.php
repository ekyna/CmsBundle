<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\EditorAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Editor\Model;

/**
 * Interface AdapterInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface AdapterInterface extends EditorAwareInterface
{
    const SIZE           = 'size';
    const ORDER          = 'order';
    const OFFSET         = 'offset';
    const PADDING_TOP    = 'padding_top';
    const PADDING_BOTTOM = 'padding_bottom';


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
     * Updates the container layout.
     *
     * @param Model\ContainerInterface $container
     * @param array                    $data // TODO Or Request ?
     *
     * @throws EditorExceptionInterface
     */
    public function updateContainerLayout(Model\ContainerInterface $container, array $data);

    /**
     * Updates the row layout.
     *
     * @param Model\RowInterface $row
     * @param array              $data // TODO Or Request ?
     *
     * @throws EditorExceptionInterface
     */
    public function updateRowLayout(Model\RowInterface $row, array $data);

    /**
     * Updates the block layout.
     *
     * @param Model\BlockInterface $block
     * @param array                $data // TODO Or Request ?
     *
     * @throws EditorExceptionInterface
     */
    public function updateBlockLayout(Model\BlockInterface $block, array $data);

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
