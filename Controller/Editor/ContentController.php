<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentController extends AbstractController
{
    /**
     * Create and append a new container to the content.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createContainer(Request $request): Response
    {
        $content = $this->findContentByRequest($request);
        $type = $request->request->get('type');

        try {
            $container = $this->editor->createDefaultContainer($type, [], $content);
        } catch (EditorExceptionInterface $e) {
            return $this->handleException($e);
        }

        $this->validate($content);
        $this->persist($content);

        $viewBuilder = $this->getViewBuilder();

        $data = [
            'created' => $viewBuilder->buildContainer($container)->getAttributes()->getId(),
            'content' => $viewBuilder->buildContent($content),
        ];

        return $this->buildResponse($data, self::SERIALIZE_FULL);
    }
}
