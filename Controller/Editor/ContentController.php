<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Exception\EditorExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ContentController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentController extends BaseController
{
    /**
     * Create and append a new container to the content.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createContainerAction(Request $request)
    {
        $content = $this->findContentByRequest($request);
        $type = $request->request->get('type', null);

        try {
            $container = $this->getEditor()->createDefaultContainer($type, [], $content);
        } catch (EditorExceptionInterface $e) {
            throw new BadRequestHttpException($e->getMessage());
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
