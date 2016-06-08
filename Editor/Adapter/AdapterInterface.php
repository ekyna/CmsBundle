<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface AdapterInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface AdapterInterface
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
}
