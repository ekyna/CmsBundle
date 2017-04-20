<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\EditorAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model;
use Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Interface AdapterInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface AdapterInterface extends EditorAwareInterface
{
    public const SIZE           = 'size';
    public const ORDER          = 'order';
    public const OFFSET         = 'offset';
    public const PADDING_TOP    = 'padding_top';
    public const PADDING_BOTTOM = 'padding_bottom';


    /**
     * Builds the content view.
     *
     * @param Model\ContentInterface $content
     * @param View\ContentView       $view
     */
    public function buildContent(Model\ContentInterface $content, View\ContentView $view): void;

    /**
     * Builds the container view.
     *
     * @param Model\ContainerInterface $container
     * @param View\ContainerView       $view
     */
    public function buildContainer(Model\ContainerInterface $container, View\ContainerView $view): void;

    /**
     * Builds the row view.
     *
     * @param Model\RowInterface $row
     * @param View\RowView       $view
     */
    public function buildRow(Model\RowInterface $row, View\RowView $view): void;

    /**
     * Builds the column view.
     *
     * @param Model\BlockInterface $block
     * @param View\BlockView       $view
     */
    public function buildBlock(Model\BlockInterface $block, View\BlockView $view): void;

    /**
     * Updates the container layout.
     *
     * @param Model\ContainerInterface $container
     * @param array                    $data // TODO Or Request ?
     *
     * @throws EditorExceptionInterface
     */
    public function updateContainerLayout(Model\ContainerInterface $container, array $data): void;

    /**
     * Updates the row layout.
     *
     * @param Model\RowInterface $row
     * @param array              $data // TODO Or Request ?
     *
     * @throws EditorExceptionInterface
     */
    public function updateRowLayout(Model\RowInterface $row, array $data): void;

    /**
     * Updates the block layout.
     *
     * @param Model\BlockInterface $block
     * @param array                $data // TODO Or Request ?
     *
     * @throws EditorExceptionInterface
     */
    public function updateBlockLayout(Model\BlockInterface $block, array $data): void;

    /**
     * Expands the block.
     *
     * @param Model\BlockInterface $block
     */
    public function expandBlock(Model\BlockInterface $block): void;

    /**
     * Compresses the block.
     *
     * @param Model\BlockInterface $block
     */
    public function compressBlock(Model\BlockInterface $block): void;

    /**
     * Pulls the block.
     *
     * @param Model\BlockInterface $block
     */
    public function pullBlock(Model\BlockInterface $block): void;

    /**
     * Pushes the block.
     *
     * @param Model\BlockInterface $block
     */
    public function pushBlock(Model\BlockInterface $block): void;

    /**
     * Offsets the block to the left.
     *
     * @param Model\BlockInterface $block
     */
    public function offsetLeftBlock(Model\BlockInterface $block): void;

    /**
     * Offsets the block to the right.
     *
     * @param Model\BlockInterface $block
     */
    public function offsetRightBlock(Model\BlockInterface $block): void;

    /**
     * Returns the block's image responsive map.
     *
     * @param Model\BlockInterface $block
     *
     * @return array
     */
    public function getImageResponsiveMap(Model\BlockInterface $block): array;
}
