<?php

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Ekyna\Component\Resource\Search;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    public function defaultAction(): Response
    {
        return $this->configureSharedCache($this->render('@EkynaCms/Cms/default.html.twig'));
    }

    /**
     * (Wide site) Search action.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request): Response
    {
        $expression = $request->request->get('expression');

        $results = $this->get(Search\Search::class)->search(new Search\Request($expression));

        return $this->render('@EkynaCms/Cms/search.html.twig', [
            'expression' => $expression,
            'results'    => $results,
        ])->setPrivate();
    }

    /**
     * Renders the footer fragment.
     *
     * @param string $locale
     *
     * @return Response
     */
    public function footerAction(string $locale): Response
    {
        $this->get('request_stack')->getCurrentRequest()->setLocale($locale);

        return $this
            ->render('@EkynaCms/Cms/Fragment/footer.html.twig')
            ->setSharedMaxAge(3600);
    }
}
