<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Ekyna\Bundle\AdminBundle\Controller\Resource\NestedTrait;
use Ekyna\Bundle\CmsBundle\Controller\Resource\ContentTrait;
use Ekyna\Bundle\CmsBundle\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;

class PageController extends ResourceController
{
    use NestedTrait;
    use ContentTrait;

    protected function generateDefaultContent()
    {
        $layout = $this->container->get('ekyna_cms.layout_registry')->get('default');

        $blocks = new ArrayCollection();
        foreach ($layout->getConfiguration() as $config) {
            $key = sprintf('ekyna_%s_block.class', $config['type']);
            if(!$this->container->hasParameter($key)) {
                throw new \InvalidArgumentException('Unknown block type "%s".', $config['type']);
            }
            $class = $this->container->getParameter($key);
            $block = new $class;
            $block
                ->setWidth($config['width'])
                ->setRow($config['row'])
                ->setColumn($config['column'])
            ;
            $blocks[] = $block;
        }

        $content = new Content();
        $content
            ->setBlocks($blocks)
            ->setVersion(1)
        ;

        return $content;
    }
}
