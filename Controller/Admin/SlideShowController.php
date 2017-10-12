<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\Context;
use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Ekyna\Bundle\CmsBundle\Entity\SlideShow;
use Ekyna\Bundle\CmsBundle\Table\Type\SlideType;

/**
 * Class SlideShowController
 * @package Ekyna\Bundle\CmsBundle\Controller\Admin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShowController extends ResourceController
{
    /**
     * @inheritDoc
     */
    protected function buildShowData(
        /** @noinspection PhpUnusedParameterInspection */
        array &$data,
        /** @noinspection PhpUnusedParameterInspection */
        Context $context
    ) {
        /** @var SlideShow $slideShow */
        $slideShow = $context->getResource();

        $table = $this
            ->getTableFactory()
            ->createTable('slides', SlideType::class, [
                'source' => $slideShow->getSlides()->toArray(),
            ]);

        if (null !== $response = $table->handleRequest($context->getRequest())) {
            return $response;
        }

        $data['slides'] = $table->createView();

        return null;
    }

}
