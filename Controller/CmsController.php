<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
     */
    public function defaultAction(Request $request)
    {
        return $this->renderCached($request);
    }

    /**
     * Returns the cached response.
     *
     * @param Request $request
     * @param string $template
     * @param array $context
     * @return Response
     */
    protected function renderCached(Request $request, $template = null, array $context = array())
    {
        if (null === $page = $this->getDoctrine()->getRepository('EkynaCmsBundle:Page')->findByRequest($request)) {
            throw new NotFoundHttpException('Page not found.');
        }

        if (null === $template) {
            $template = $this->container->getParameter('ekyna_cms.default_template');
        }

        $response = new Response();
        if (!$this->isAdminUser()) {
            $response
                ->setPublic()
                ->setMaxAge($this->container->getParameter('ekyna_cms.default_max_age'))
                ->setLastModified($page->getUpdatedAt())
            ;
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        return $response->setContent($this->renderView($template, $context));
    }
}
