<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Menu;

use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use Ekyna\Bundle\ResourceBundle\Service\Http\TagManager;
use Ekyna\Component\Resource\Locale\LocaleProviderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use RuntimeException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BreadcrumbBuilder
 * @package Ekyna\Bundle\CmsBundle\Service\Menu
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BreadcrumbBuilder
{
    protected FactoryInterface        $factory;
    protected RouterInterface         $router;
    protected PageHelper              $pageHelper;
    protected LocaleProviderInterface $localeProvider;
    protected TagManager              $tagManager;
    protected ?ItemInterface          $breadcrumb = null;


    /**
     * Constructor.
     *
     * @param FactoryInterface        $factory
     * @param RouterInterface         $router
     * @param PageHelper              $pageHelper
     * @param LocaleProviderInterface $localeProvider
     * @param TagManager              $tagManager
     */
    public function __construct(
        FactoryInterface $factory,
        RouterInterface $router,
        PageHelper $pageHelper,
        LocaleProviderInterface $localeProvider,
        TagManager $tagManager
    ) {
        $this->factory = $factory;
        $this->router = $router;
        $this->pageHelper = $pageHelper;
        $this->localeProvider = $localeProvider;
        $this->tagManager = $tagManager;
    }

    /**
     * Appends an item to the breadcrumb.
     *
     * @param string      $name
     * @param string      $label
     * @param string|null $route
     * @param array       $parameters
     *
     * @throws RuntimeException
     */
    public function breadcrumbAppend(string $name, string $label, string $route = null, array $parameters = [])
    {
        if (null === $this->breadcrumb) {
            $this->createBreadcrumb();
        }

        $this
            ->breadcrumb
            ->addChild($name, ['route' => $route, 'routeParameters' => $parameters])
            ->setLabel($label);
    }

    /**
     * Create if not exists and returns the breadcrumb.
     *
     * @return ItemInterface
     */
    public function createBreadcrumb(): ItemInterface
    {
        if ($this->breadcrumb) {
            return $this->breadcrumb;
        }

        $this->createBreadcrumbRoot();

        // Retrieve the current page.
        $currentPage = $this->pageHelper->getCurrent();

        // If not found look for a parent
        if (null === $currentPage && null !== $request = $this->pageHelper->getRequest()) {
            // TODO create a route finder ? (same in AdminBundle ResourceHelper)
            $routeName = $request->attributes->get('_route');
            $route = $this->router->getRouteCollection()->get($routeName);
            if (null !== $route) {
                $cmsOptions = $route->getOption('_cms');
                if (null !== $cmsOptions
                    && array_key_exists('parent', $cmsOptions)
                    && !empty($cmsOptions['parent'])
                ) {
                    $currentPage = $this->pageHelper->findByRoute($cmsOptions['parent']);
                }
            }
        }

        // If found, build the breadcrumb
        if (null !== $currentPage) {
            $repository = $this->pageHelper->getPageRepository();
            $pages = $repository->findParentsForBreadcrumb($currentPage);

            // Fill the menu
            foreach ($pages as $page) {
                if ($page['dynamicPath']) {
                    $params = ['uri' => null];
                } else {
                    $params = ['route' => $page['route']];
                }

                $this->breadcrumb
                    ->addChild('page-' . $page['id'], $params)
                    ->setLabel($page['breadcrumb']);

                $this->tagManager->addTags(sprintf('%s[id:%s]', $repository->getCachePrefix(), $page['id']));
            }
        }


        return $this->breadcrumb;
    }

    /**
     * Creates the breadcrumb root item.
     */
    private function createBreadcrumbRoot()
    {
        if ($this->breadcrumb) {
            return;
        }

        if (null === $home = $this->pageHelper->getHomePage()) {
            throw new RuntimeException('Home page not found.');
        }

        $this->breadcrumb = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'breadcrumb hidden-xs',
            ],
        ]);

        $this->tagManager->addTags($home->getEntityTag());
    }
}
