<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Adapter;

use Ekyna\Bundle\CmsBundle\Editor\View;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class Bootstrap3Adapter
 * @package Ekyna\Bundle\CmsBundle\Editor\Adapter
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Bootstrap3Adapter implements AdapterInterface
{
    /**
     * @inheritDoc
     */
    public function buildContent(Model\ContentInterface $content, View\ContentView $view)
    {
        $view->attributes = array('classes' => 'content');
    }

    /**
     * @inheritDoc
     */
    public function buildContainer(Model\ContainerInterface $container, View\ContainerView $view)
    {
        $view->innerAttributes = array('classes' => 'container');
    }

    /**
     * @inheritDoc
     */
    public function buildRow(Model\RowInterface $row, View\RowView $view)
    {
        $view->attributes = array('classes' => 'row');
    }

    /**
     * @inheritDoc
     */
    public function buildBlock(Model\BlockInterface $block, View\BlockView $view)
    {
        $view->attributes = array('classes' => 'col-md-' . $block->getSize());
    }
}
