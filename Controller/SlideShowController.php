<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SlideShowController
 * @package Ekyna\Bundle\CmsBundle\Controller
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShowController extends Controller
{
    public function typesAction()
    {
        $config = [];

        $types = $this->get('ekyna_cms.slide_show.registry')->all();

        foreach ($types as $type) {
            $config[$type->getName()] = $type->getJsPath();
        }

        $response = new JsonResponse($config);
        $response
            ->setPublic()
            ->setMaxAge(3600 * 6)
            ->setSharedMaxAge(3600 * 6);

        return $response;
    }
}
