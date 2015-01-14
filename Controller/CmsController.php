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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function defaultAction(Request $request)
    {
        if (null === $page = $this->getDoctrine()->getRepository('EkynaCmsBundle:Page')->findOneByRequest($request)) {
            throw new NotFoundHttpException('Page not found');
        }

        return $this
            ->render('EkynaCmsBundle:Cms:default.html.twig')
            ->setPublic()
            ->setSharedMaxAge($this->container->getParameter('ekyna_cms.default_max_age'))
        ;
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
