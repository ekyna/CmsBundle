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
    public function buildContent(Model\ContentInterface $content, View\Content $view)
    {
        $view->attributes = array('class' => 'content');
    }

    /**
     * @inheritDoc
     */
    public function buildContainer(Model\ContainerInterface $container, View\Container $view)
    {
        $view->attributes = array('class' => 'container');
    }

    /**
     * @inheritDoc
     */
    public function buildRow(Model\BlockInterface $block, View\Row $view)
    {
        $view->attributes = array('class' => 'row');
    }

    /**
     * @inheritDoc
     */
    public function buildBlock(Model\BlockInterface $block, View\Block $view)
    {
        $view->columnAttributes = array('class' => 'col-md-' . $block->getSize());
    }
}
