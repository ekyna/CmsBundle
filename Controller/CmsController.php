<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CmsController
 * @package Ekyna\Bundle\CmsBundle\Controller
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsController extends Controller
{
    /**
     * Default action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction()
    {
        return $this->configureSharedCache($this->render('EkynaCmsBundle:Cms:default.html.twig'));
    }

    /**
     * (Wide site) Search action.
     */
    public function searchAction()
    {
        // TODO site wide search providers
    }

    /**
     * Renders the cms init (xhr only).
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function initAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $data = [];

        // Does flashes must be rendered ?
        if ($request->request->get('flashes', false)) {
            $data['flashes'] = true;
        } else {
            $data['flashes'] = false;
        }

        // Does editor must be rendered ?
        if ($request->request->get('editor', false) && $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $data['editor'] = true;
        } else {
            $data['editor'] = false;
        }

        // Does cookie consent must be rendered ?
        if ($request->request->get('cookie', false)) {
            if ($this->container->getParameter('ekyna_cms.cookie_consent.config')['enable']) {
                $data['cookie_consent'] = true;
            } else {
                $data['cookie_consent'] = false;
            }
        } else {
            $data['cookie_consent'] = null;
        }

        $response = $this->render('EkynaCmsBundle:Cms:init.xml.twig', $data);
        $response->headers->add(array('Content-Type' => 'application/xml'));

        return $response;
    }

    /**
     * Renders the flash.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function flashesAction()
    {
        return $this
            ->render('EkynaCmsBundle:Cms:flashes.html.twig')
            ->setPrivate()
        ;
    }
}
