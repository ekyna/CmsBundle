<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Factory\MenuFactoryInterface;
use Ekyna\Bundle\CmsBundle\Factory\PageFactoryInterface;
use Ekyna\Bundle\CmsBundle\Factory\SeoFactoryInterface;
use Ekyna\Bundle\CmsBundle\Manager\MenuManagerInterface;
use Ekyna\Bundle\CmsBundle\Manager\PageManagerInterface;
use Ekyna\Bundle\CmsBundle\Model\ChangeFrequencies;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Repository\MenuRepositoryInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Ekyna\Bundle\CmsBundle\Service\Helper\RoutingHelper;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function array_key_exists;
use function array_merge;
use function array_replace;
use function is_array;
use function is_null;
use function is_scalar;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * Class PageGenerator
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageGenerator
{
    private PageManagerInterface    $pageManager;
    private PageRepositoryInterface $pageRepository;
    private PageFactoryInterface    $pageFactory;

    private MenuManagerInterface    $menuManager;
    private MenuRepositoryInterface $menuRepository;
    private MenuFactoryInterface    $menuFactory;

    private SeoFactoryInterface $seoFactory;

    private ValidatorInterface     $validator;
    private RoutingHelper          $routingHelper;
    private EntityManagerInterface $entityManager;
    private array                  $locales;
    private string                 $homeRouteName;

    private OutputInterface $output;
    private OptionsResolver $optionsResolver;
    private RouteDefinition $homeDefinition;

    public function __construct(
        PageManagerInterface    $pageManager,
        PageRepositoryInterface $pageRepository,
        PageFactoryInterface    $pageFactory,
        MenuManagerInterface    $menuManager,
        MenuRepositoryInterface $menuRepository,
        MenuFactoryInterface    $menuFactory,
        SeoFactoryInterface     $seoFactory,
        ValidatorInterface      $validator,
        RoutingHelper           $routingHelper,
        EntityManagerInterface  $entityManager,
        array                   $locales,
        string                  $homeRouteName
    ) {
        $this->pageManager = $pageManager;
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $pageFactory;
        $this->menuManager = $menuManager;
        $this->menuRepository = $menuRepository;
        $this->menuFactory = $menuFactory;
        $this->seoFactory = $seoFactory;
        $this->validator = $validator;
        $this->routingHelper = $routingHelper;
        $this->entityManager = $entityManager;
        $this->locales = $locales;
        $this->homeRouteName = $homeRouteName;
    }

    /**
     * Generates pages based on routing '_cms' option.
     */
    public function generate(OutputInterface $output): void
    {
        $this->output = $output;

        $this->configureOptionsResolver();
        $this->gatherRoutesDefinitions();

        $this->createPage($this->homeDefinition);

        $this->removeNonMappedPages();

        $this->pageManager->clear();
    }

    /**
     * Removes all menus.
     */
    public function truncate(OutputInterface $output): void
    {
        $output->writeln('Removing pages ...');

        $count = 0;
        $pages = $this->pageRepository->findAll();
        foreach ($pages as $page) {
            $this->pageManager->remove($page);
            $count++;
        }
        $this->pageManager->flush();
        $this->pageManager->clear();

        $metadata = $this->entityManager->getClassMetadata($this->pageRepository->getClassName());
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();

        try {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSQL($metadata->getTableName());
            $connection->executeQuery($q);
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (Exception $e) {
            $output->writeln('<error>Failed to remove pages.</error>');
            $connection->rollBack();
        }

        $output->writeln('<info>Pages purged.</info>');
    }

    /**
     * Configures the option resolver.
     */
    private function configureOptionsResolver(): void
    {
        /**
         * Seo options
         */
        $seoOptionResolver = new OptionsResolver();
        $seoOptionResolver
            ->setDefaults([
                'changefreq' => 'monthly',
                'priority'   => 0.5,
                'follow'     => true,
                'index'      => true,
                'canonical'  => null,
            ])
            ->setAllowedTypes('changefreq', 'string')
            ->setAllowedTypes('priority', 'float')
            ->setAllowedTypes('follow', 'bool')
            ->setAllowedTypes('index', 'bool')
            ->setAllowedTypes('canonical', ['string', 'null'])
            ->setAllowedValues('changefreq', ChangeFrequencies::getConstants())
            ->setNormalizer('priority', function (Options $options, $value) {
                if (0 > $value) {
                    return 0;
                }
                if (1 < $value) {
                    return 1;
                }

                return $value;
            });

        /**
         * Menus options
         */
        $menuOptionResolver = new OptionsResolver();
        $menuOptionResolver
            ->setDefaults([
                'options'    => [],
                'attributes' => [],
            ])
            ->setAllowedTypes('options', 'array')
            ->setAllowedTypes('attributes', 'array');

        /**
         * Page options
         */
        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver
            ->setDefaults([
                'name'     => null,
                'title'    => null,
                'path'     => null,
                'parent'   => null,
                'locked'   => true,
                'advanced' => false,
                'dynamic'  => false,
                'position' => 0,
                'seo'      => null,
                'menus'    => [],
            ])
            ->setRequired(['name', 'path'])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('title', ['null', 'string', 'array'])
            ->setAllowedTypes('path', 'string')
            ->setAllowedTypes('parent', ['string', 'null'])
            ->setAllowedTypes('locked', 'bool')
            ->setAllowedTypes('advanced', 'bool')
            ->setAllowedTypes('dynamic', 'bool')
            ->setAllowedTypes('position', 'int')
            ->setAllowedTypes('seo', ['null', 'array'])
            ->setAllowedTypes('menus', 'array')
            ->setAllowedValues('title', function ($value) {
                if (is_null($value)) {
                    return true;
                }

                if (is_string($value) && !empty($value)) {
                    return true;
                }

                if (is_array($value)) {
                    if (empty($value)) {
                        return false;
                    }

                    foreach ($value as $locale => $title) {
                        if (!preg_match('~^[a-z]{2}$~', $locale)) {
                            return false;
                        }
                        if (!is_string($title) && !empty($title)) {
                            return false;
                        }
                    }

                    return true;
                }

                return false;
            })
            ->setNormalizer('locked', function (Options $options, $value) {
                // Lock pages with parameters in path
                if (preg_match('#{[^}]+}#', $options['path'])) {
                    return true;
                }

                return $value;
            })
            ->setNormalizer('menus', function (Options $options, $value) use ($menuOptionResolver) {
                $normalized = [];
                if (!empty($value)) {
                    foreach ($value as $key => $val) {
                        if (is_scalar($val)) {
                            $normalized[$val] = [];
                        } elseif (is_scalar($key) && null == $val) {
                            $normalized[$key] = [];
                        } elseif (is_array($val)) {
                            $normalized[$key] = $menuOptionResolver->resolve($val);
                        } else {
                            throw new InvalidArgumentException('Unexpected menu options format.');
                        }
                    }
                }

                return $normalized;
            })
            ->setNormalizer('seo', function (Options $options, $value) use ($seoOptionResolver) {
                return $seoOptionResolver->resolve((array)$value);
            });
    }

    /**
     * Creates a tree of RouteDefinition.
     */
    private function gatherRoutesDefinitions(): void
    {
        if (!$route = $this->routingHelper->findRouteByName($this->homeRouteName, null)) {
            throw new RuntimeException("Route '$this->homeRouteName' not found.");
        }

        $this->homeDefinition = new RouteDefinition(
            $this->homeRouteName,
            $this->resolveRouteOptions($route, $this->homeRouteName)
        );

        $routes = $this->routingHelper->getRoutes();

        /** @var Route $route */
        foreach ($routes as $name => $route) {
            // Skip routes without _cms.name option
            if (is_null($cms = $route->getOption('_cms')) || !array_key_exists('name', $cms)) {
                continue;
            }

            // Skip home page
            if ($name === $this->homeRouteName) {
                $this->handleRouteLocalization($name, $route, $this->homeDefinition);

                continue;
            }

            $this->createRouteDefinition($name, $route);
        }

        $this->homeDefinition->sortChildren();
    }

    /**
     * Handles the route localization.
     */
    private function handleRouteLocalization(string $name, Route $route, RouteDefinition $definition): void
    {
        if (null === $locale = $route->getDefault('_locale')) {
            return;
        }

        $options = $this->resolveRouteOptions($route, $name);

        $title = $options['name'];
        if (isset($options['title'])) {
            if (is_array($options['title'])) {
                $title = $options['title'][$locale] ?? $title;
            } else {
                $title = $options['title'] ?? $title;
            }
        }

        $definition->addLocalization($locale, $title, $name);
    }

    /**
     * Resolves route options.
     */
    private function resolveRouteOptions(Route $route, string $routeName): array
    {
        if (null === $cmsOptions = $route->getOption('_cms')) {
            throw new InvalidArgumentException(sprintf(
                'Route "%s" does not have "_cms" defaults attributes.',
                $routeName
            ));
        }

        $locale = $route->getDefault('_locale');

        $options = array_merge($cmsOptions, [
            'path'    => $this->routingHelper->buildPagePath($routeName, $locale),
            'dynamic' => $this->routingHelper->isPagePathDynamic($routeName, $locale),
        ]);

        return $this->optionsResolver->resolve($options);
    }

    /**
     * Creates a route definition
     */
    private function createRouteDefinition(string $routeName, Route $route): RouteDefinition
    {
        $canonical = $this->getRouteCanonicalName($routeName, $route);

        if ($definition = $this->findRouteDefinitionByRouteName($canonical)) {
            $this->handleRouteLocalization($routeName, $route, $definition);

            return $definition;
        }

        $options = $this->resolveRouteOptions($route, $routeName);

        $definition = new RouteDefinition($canonical, $options);

        $this->handleRouteLocalization($routeName, $route, $definition);

        if (null === $parentRouteName = $definition->getParentRouteName()) {
            // If parent route name is null => home page child
            $definition->setParentRouteName($this->homeRouteName);
            $this->homeDefinition->appendChild($definition);

            return $definition;
        }

        // Creates parent route definition if needed
        if (!$parentRoute = $this->routingHelper->findRouteByName($parentRouteName, null)) {
            throw new RuntimeException("Route '$parentRouteName' not found.");
        }

        $parentDefinition = $this->createRouteDefinition($parentRouteName, $parentRoute);
        $parentDefinition->appendChild($definition);

        return $definition;
    }

    /**
     * Returns the route canonical name.
     */
    private function getRouteCanonicalName(string $routeName, Route $route): string
    {
        return $route->hasDefault('_canonical_route')
            ? $route->getDefault('_canonical_route')
            : $routeName;
    }

    /**
     * Finds a RouteDefinition by route name
     */
    private function findRouteDefinitionByRouteName(string $routeName): ?RouteDefinition
    {
        if ($routeName === $this->homeRouteName) {
            return $this->homeDefinition;
        }

        return $this->homeDefinition->findChildByRouteName($routeName);
    }

    /**
     * Creates a Page from given Route
     */
    private function createPage(RouteDefinition $definition, PageInterface $parentPage = null): bool
    {
        $routeName = $definition->getRouteName();

        if (null !== $page = $this->pageRepository->findOneByRoute($routeName)) {
            $updated = false;
            if ($page->getName() !== $definition->getPageName()) {
                $page->setName($definition->getPageName());
                $updated = true;
            }
            if ($page->isLocked() !== $definition->isLocked()) {
                $page->setLocked($definition->isLocked());
                $updated = true;
            }
            if ($page->isAdvanced() !== $definition->isAdvanced()) {
                $page->setAdvanced($definition->isAdvanced());
                $updated = true;
            }
            if ($page->isDynamicPath() !== $definition->isDynamic()) {
                $page->setDynamicPath($definition->isDynamic());
                $updated = true;
            }

            // Watch for paths update
            foreach ($this->locales as $locale) {
                $localization = array_replace([
                    'route' => $routeName,
                ], (array)$definition->getLocalization($locale));

                $path = $this
                    ->routingHelper
                    ->buildPagePath($localization['route'], $locale);

                $pageTranslation = $page->translate($locale, true);

                if ($pageTranslation->getPath() !== $path) {
                    $pageTranslation->setPath($path);
                    $updated = true;
                }
            }

            if ($updated) {
                if (!$this->validate($page)) {
                    return false;
                }
                $this->pageManager->save($page);
            }

            $this->outputPageAction($page->getName(), $updated ? 'updated' : 'already exists');
        } else {
            // Seo
            $seoDefinition = $definition->getSeo();
            $seo = $this->seoFactory->create();
            $seo
                ->setChangefreq($seoDefinition['changefreq'])
                ->setPriority((string)$seoDefinition['priority'])
                ->setFollow((bool)$seoDefinition['follow'])
                ->setIndex((bool)$seoDefinition['index'])
                ->setCanonical($seoDefinition['canonical']);

            // Page
            $page = $this->pageFactory->create();
            $page
                ->setName($definition->getPageName())
                ->setRoute($routeName)
                ->setPath($definition->getPath())
                ->setStatic(true)
                ->setLocked($definition->isLocked())
                ->setAdvanced($definition->isAdvanced())
                ->setDynamicPath($definition->isDynamic())
                ->setParent($parentPage)
                ->setSeo($seo);

            foreach ($this->locales as $locale) {
                $localization = array_replace([
                    'title' => $definition->getPageName(),
                    'route' => $routeName,
                ], (array)$definition->getLocalization($locale));

                $path = $this
                    ->routingHelper
                    ->buildPagePath($localization['route'], $locale);

                $title = $seoTitle = $localization['title'];

                if (null !== $parentPage && $parentPage->getRoute() !== $this->homeRouteName) {
                    $seoTitle = sprintf('%s - %s', $parentPage->getSeo()->translate($locale)->getTitle(), $title);
                }

                $seoTranslation = $seo->translate($locale, true);
                $seoTranslation
                    ->setTitle($seoTitle);

                $pageTranslation = $page->translate($locale, true);
                $pageTranslation
                    ->setTitle($title)
                    ->setBreadcrumb($title)
                    ->setPath($path);
            }

            if (!$this->validate($page)) {
                return false;
            }

            $this->pageManager->save($page);

            $this->outputPageAction($page->getName(), 'created');
        }

        if (!$this->createMenus($page, $definition->getMenus())) {
            return false;
        }

        // Creates children pages
        foreach ($definition->getChildren() as $child) {
            if (!$this->createPage($child, $page)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the element.
     */
    private function validate(object $element): bool
    {
        $violationList = $this->validator->validate($element, null, ['Generator']);

        if (0 === $violationList->count()) {
            return true;
        }

        $this->output->writeln('<error>Invalid element</error>');

        foreach ($violationList as $violation) {
            $this->output->writeln(sprintf(
                '<error>%s : %s</error>',
                $violation->getPropertyPath(),
                $violation->getMessage()
            ));
        }

        return false;
    }

    /**
     * Outputs the page action.
     */
    private function outputPageAction(string $name, string $action): void
    {
        $this->output->writeln(sprintf(
            '- <comment>%s</comment> %s %s.',
            $name,
            str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT),
            $action
        ));
    }

    /**
     * Creates the menus entries.
     */
    private function createMenus(PageInterface $page, array $menus): bool
    {
        if (!empty($menus)) {
            foreach ($menus as $parentName => $config) {
                if (null === $parent = $this->menuRepository->findOneByName($parentName)) {
                    $this->output->writeln(sprintf(
                        '<error>Parent menu "%s" not found for route "%s".</error>',
                        $parentName,
                        $page->getRoute()
                    ));

                    return false;
                }

                $name = $page->getRoute();
                if (null === $this->menuRepository->findOneBy(['name' => $name, 'parent' => $parent])) {
                    $menu = $this->menuFactory->create();
                    $menu
                        ->setParent($parent)
                        ->setName($name)
                        ->setRoute($name);

                    if (isset($config['options'])) {
                        $menu->setOptions((array)$config['options']);
                    }
                    if (isset($config['attributes'])) {
                        $menu->setAttributes((array)$config['attributes']);
                    }

                    foreach ($this->locales as $locale) {
                        $menuTranslation = $menu->translate($locale, true);
                        $menuTranslation
                            ->setTitle($page->translate($locale)->getTitle());
                    }

                    if (!$this->validate($menu)) {
                        return false;
                    }

                    $this->menuManager->save($menu);
                }
            }
        }

        return true;
    }

    /**
     * Removes static pages which are no longer mapped to the routing.
     */
    private function removeNonMappedPages(): void
    {
        $pages = $this->pageRepository->findBy(['static' => true], ['left' => 'DESC']);

        foreach ($pages as $page) {
            if (null !== $this->findRouteDefinitionByRouteName($page->getRoute())) {
                continue;
            }

            $this->outputPageAction($page->getName(), 'removed');

            $this->pageManager->delete($page);
        }
    }
}
