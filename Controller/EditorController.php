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
     * REnders the CMS Editor.
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('EkynaCmsBundle:Editor:index.html.twig');
    }




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

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
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
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Expected administrator.');
        }

        $editor = $this->get('ekyna_cms.editor');
        if (0 < $contentId = intval($request->request->get('contentId', 0))) {
            $editor->initContent($contentId);
        }

        $responseData = [];

        // Update layout
        if (null !== $layoutData = $request->request->get('layout', null)) {
            $editor->updateLayout($layoutData);
        }

        // Remove blocks
        if (null !== $removeData = $request->request->get('removeBlocks', null)) {
            $responseData['removed'] = $editor->removeBlocks($removeData);
        }

        // Update block
        if (null !== $updateData = $request->request->get('updateBlock', null)) {
            $responseData['updated'] = $editor->updateBlock($updateData);
        }

        // Create block
        if (null !== $createData = $request->request->get('createBlock', null)) {
            $responseData['created'] = $editor->createBlock($createData);
        }

        return new JsonResponse($responseData);
    }
}
