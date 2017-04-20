<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Class EditorController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EditorController
{
    private Editor                  $editor;
    private Environment             $twig;
    private UrlGeneratorInterface   $urlGenerator;
    private PageRepositoryInterface $pageRepository;
    private string                  $homeRoute;


    /**
     * Constructor.
     *
     * @param Editor                  $editor
     * @param Environment             $twig
     * @param UrlGeneratorInterface   $urlGenerator
     * @param PageRepositoryInterface $pageRepository
     * @param string                  $homeRoute
     */
    public function __construct(
        Editor $editor,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        PageRepositoryInterface $pageRepository,
        string $homeRoute
    ) {
        $this->editor = $editor;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->pageRepository = $pageRepository;
        $this->homeRoute = $homeRoute;
    }

    /**
     * Renders the CMS Editor.
     *
     * @param Request $request
     *
     * @return Response
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function index(Request $request): Response
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $content = $this->twig->render('@EkynaCms/Editor/index.html.twig', [
            'config' => $this->buildConfig($request),
        ]);

        $response = new Response($content);

        return $response->setPrivate();
    }

    /**
     * Builds the editor configuration.
     *
     * @param Request $request
     *
     * @return array
     */
    private function buildConfig(Request $request): array
    {
        $config = $this->editor->getConfig();
        unset($config['layout']);

        $locales = [];
        foreach ($config['locales'] as $locale) {
            $locales[] = [
                'name'  => $locale,
                'value' => $locale,
                'title' => ucfirst(Locales::getName($locale)),
            ];
        }
        $config['locales'] = $locales;

        $config['hostname'] = $request->getHost();
        if (!empty($path = $request->query->get('path'))) {
            $config['path'] = $path;
        } else {
            $config['path'] = $this->urlGenerator->generate($this->homeRoute);
        }
        $config['plugins'] = $this->editor->getPluginsConfig();

        return $config;
    }

    /**
     * Pages list action.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function pagesList(Request $request): Response
    {
        $lastModifiedAt = $this->pageRepository->getLastUpdatedAt();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setLastModified($lastModifiedAt);

        if ($response->isNotModified($request)) {
            return $response;
        }

        $documentLocale = $request->query->get('document_locale', $request->getLocale());
        $list = [];

        /** @var PageInterface[] $pages */
        $pages = $this
            ->pageRepository
            ->findBy(['dynamicPath' => false], ['left' => 'ASC']);

        foreach ($pages as $page) {
            $list[] = $this->pageToArray($page, $documentLocale);
        }

        return $response->setContent(json_encode($list));
    }

    /**
     * Returns page array representation.
     *
     * @param PageInterface $page
     * @param string        $locale
     *
     * @return array
     */
    private function pageToArray(PageInterface $page, string $locale): array
    {
        $tabs = '';
        for ($l = 0; $l < $page->getLevel(); $l++) {
            $tabs .= ' • ';
        }

        return [
            'value' => $page->getId(),
            'title' => $tabs . $page->translate($locale)->getTitle(),
            'data'  => [
                'locked' => $page->isLocked(),
                'path'   => $this->urlGenerator->generate(
                    $page->getRoute(),
                    ['_locale' => $locale],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ],
        ];
    }
}
