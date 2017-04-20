<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SlideShowController
 * @package Ekyna\Bundle\CmsBundle\Controller
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShowController
{
    private TypeRegistryInterface $registry;

    public function __construct(TypeRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Slide show types.
     */
    public function types(): JsonResponse
    {
        $config = [];

        $types = $this->registry->all();

        foreach ($types as $type) {
            $config[$type->getName()] = $type->getJsPath();
        }

        $response = new JsonResponse($config);
        $response
            ->setPublic()
            ->setMaxAge(3600 * 24 * 30)
            ->setSharedMaxAge(3600 * 24 * 30);

        return $response;
    }
}
