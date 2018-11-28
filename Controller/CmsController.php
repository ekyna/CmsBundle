<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CmsController
 * @package Ekyna\Bundle\CmsBundle\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
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
        return $this->configureSharedCache($this->render('@EkynaCms/Cms/default.html.twig'));
    }

    /**
     * (Wide site) Search action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $expression = $request->request->get('expression');

        $results = $this->get('ekyna_cms.wide_search')->search($expression);

        return $this->render('@EkynaCms/Cms/search.html.twig', array(
            'expression' => $expression,
            'results'    => $results,
        ))->setPrivate();
    }

    /**
     * Cookie consent action (xhr only).
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cookieConsentAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $enabled = $this
            ->container
            ->getParameter('ekyna_cms.page.config')['cookie_consent']['enable'];

        if (!$enabled) {
            throw new NotFoundHttpException();
        }

        $response = $this->render('@EkynaCms/Cms/Cookie/response.xml.twig');

        $response->headers->add(['Content-Type' => 'application/xml']);

        return $response->setPrivate();
    }

    /**
     * Renders the footer fragment.
     *
     * @param string $locale
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function footerAction($locale)
    {
        $this->get('request_stack')->getCurrentRequest()->setLocale($locale);

        return $this
            ->render('@EkynaCms/Cms/Fragment/footer.html.twig')
            ->setSharedMaxAge(3600);
    }
}
