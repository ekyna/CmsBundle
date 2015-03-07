<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EditorController
 * @package Ekyna\Bundle\CmsBundle\Controller
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EditorController extends Controller
{
    /**
     * Toolbar action (front).
     *
     * @param Request $request
     * @return Response
     */
    public function initAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $response = new Response();
        $response->setPrivate();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $response->setContent($this->renderView('EkynaCmsBundle:Editor:editor.html.twig'));
        }

        return $response;
    }

    /**
     * Handles an editor request.
     * 
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function requestAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        // TODO improve security with acls
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Expected administrator.');
        }

        $editor = $this->get('ekyna_cms.editor');
        if (0 < $contentId = intval($request->request->get('contentId', 0))) {
            $editor->initContent($contentId);
        }

        $responseDatas = array();

        // Update layout
        if (null !== $layoutDatas = $request->request->get('layout', null)) {
            $editor->updateLayout($layoutDatas);
        }

        // Remove blocks
        if (null !== $removeDatas = $request->request->get('removeBlocks', null)) {
            $responseDatas['removed'] = $editor->removeBlocks($removeDatas);
        }

        // Update block
        if (null !== $updateDatas = $request->request->get('updateBlock', null)) {
            $responseDatas['updated'] = $editor->updateBlock($updateDatas);
        }

        // Create block
        if (null !== $createDatas = $request->request->get('createBlock', null)) {
            $responseDatas['created'] = $editor->createBlock($createDatas);
        }

        return new JsonResponse($responseDatas);
    }
}
