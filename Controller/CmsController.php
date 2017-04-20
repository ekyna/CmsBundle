<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Controller;

use Ekyna\Component\Resource\Search;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Class CmsController
 * @package Ekyna\Bundle\CmsBundle\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CmsController
{
    private Environment   $twig;
    private Search\Search $search;
    private RequestStack  $requestStack;

    public function __construct(Environment $twig, Search\Search $search, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->search = $search;
        $this->requestStack = $requestStack;
    }

    /**
     * Page action.
     */
    public function page(): Response
    {
        $content = $this->twig->render('@EkynaCms/Cms/page.html.twig');

        return new Response($content);
    }

    /**
     * (Wide site) Search action.
     */
    public function search(Request $request): Response
    {
        $expression = $request->request->get('expression');

        $results = $this->search->search(new Search\Request($expression));

        $content = $this->twig->render('@EkynaCms/Cms/search.html.twig', [
            'expression' => $expression,
            'results'    => $results,
        ]);

        $response = new Response($content);

        return $response->setPrivate();
    }

    /**
     * Renders the footer fragment.
     */
    public function footer(string $locale): Response
    {
        $this->requestStack->getCurrentRequest()->setLocale($locale);

        $content = $this->twig->render('@EkynaCms/Cms/Fragment/footer.html.twig');

        $response = new Response($content);

        return $response->setSharedMaxAge(3600);
    }
}
