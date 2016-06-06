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
     * @param View\Content           $view
     */
    public function buildContent(Model\ContentInterface $content, View\Content $view);

    /**
     * Builds the container view.
     *
     * @param Model\ContainerInterface $container
     * @param View\Container           $view
     */
    public function buildContainer(Model\ContainerInterface $container, View\Container $view);

    /**
     * Builds the row view.
     *
     * @param Model\BlockInterface $block
     * @param View\Row             $view
     */
    public function buildRow(Model\BlockInterface $block, View\Row $view);

    /**
     * Builds the column view.
     *
     * @param Model\BlockInterface $block
     * @param View\Block           $view
     */
    public function buildBlock(Model\BlockInterface $block, View\Block $view);
}
