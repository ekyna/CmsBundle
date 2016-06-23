<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

/**
 * Class EditorController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EditorController extends BaseController
{
    /**
     * Renders the CMS Editor.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('EkynaCmsBundle:Editor:index.html.twig', [
            'config' => $this->buildConfig($request),
        ]);
    }

    /**
     * Pages list action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pagesListAction(Request $request)
    {
        $documentLocale = $request->query->get('document_locale', $request->getLocale());
        $list = [];

        /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface[] $pages */
        $pages = $this->get('ekyna_cms.page.repository')->findBy([], ['left' => 'ASC']);

        foreach ($pages as $page) {
            $list[] = $this->pageToArray($page, $documentLocale);
        }

        $response = new Response(json_encode($list));
        $response->headers->set('Content-Type', 'application/json');
        $response->setMaxAge(3600);

        return $response;
    }

    /**
     * Returns page array representation.
     *
     * @param PageInterface $page
     * @param string        $locale
     *
     * @return array
     */
    private function pageToArray(PageInterface $page, $locale)
    {
        return [
            'value'    => $page->getId(),
            'title'    => $page->translate($locale)->getTitle(),
            'disabled' => $page->getDynamicPath(),
            'data'     => [
                'locked' => $page->getLocked(),
                'path'   => $this->generateUrl($page->getRoute(), ['_locale' => $locale]),
            ],
        ];
    }

    /**
     * Builds the editor configuration.
     *
     * @param Request $request
     *
     * @return array
     */
    private function buildConfig(Request $request)
    {
        $editor = $this->getEditor();

        $config = $editor->getConfig();
        unset($config['layout']);

        $locales = [];
        foreach ($config['locales'] as $locale) {
            $locales[] = [
                'name'  => $locale,
                'value' => $locale,
                'title' => ucfirst(Intl::getLocaleBundle()->getLocaleName($locale)),
            ];
        }
        $config['locales'] = $locales;

        $config['hostname'] = $request->getHost();
        $config['path'] = $this->generateUrl($this->getParameter('ekyna_cms.home_route'));
        $config['plugins'] = $editor->getPluginsConfig();

        return $config;
    }
}
