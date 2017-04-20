<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use LogicException;

use function array_key_exists;
use function count;
use function sprintf;
use function trim;
use function uasort;

/**
 * Class RouteDefinition
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RouteDefinition
{
    protected string  $routeName;
    protected ?string $parentRouteName;
    protected string  $pageName;
    protected string  $path;
    protected array   $localizations;
    protected bool    $locked;
    protected bool    $advanced;
    protected bool    $dynamic;
    protected int     $position = 0;
    protected array   $seo;
    protected array   $menus;
    protected array   $children;


    /**
     * Constructor
     *
     * @param string $routeName
     * @param array  $options
     */
    public function __construct(string $routeName, array $options)
    {
        $this->routeName = $routeName;
        $this->parentRouteName = $options['parent'];

        $this->pageName = $options['name'];
        $this->path = '/' . trim($options['path'], '/');
        $this->locked = $options['locked'];
        $this->advanced = $options['advanced'];
        $this->dynamic = $options['dynamic'];
        $this->position = $options['position'];
        $this->seo = $options['seo'];
        $this->menus = $options['menus'];

        $this->localizations = [];
        $this->children = [];
    }

    /**
     * Returns the route name
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * Returns the parent route name
     *
     * @return string|null
     */
    public function getParentRouteName(): ?string
    {
        return $this->parentRouteName;
    }

    /**
     * Sets the parent route name
     *
     * @param string|null $name
     *
     * @return RouteDefinition
     */
    public function setParentRouteName(?string $name): RouteDefinition
    {
        $this->parentRouteName = $name;

        return $this;
    }

    /**
     * Returns the page name
     */
    public function getPageName(): string
    {
        return $this->pageName;
    }

    /**
     * Returns the route path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Adds the localization.
     *
     * @param string $locale
     * @param string $title
     * @param string $route
     *
     * @return $this
     */
    public function addLocalization(string $locale, string $title, string $route): RouteDefinition
    {
        $this->localizations[$locale] = [
            'title' => $title,
            'route' => $route,
        ];

        return $this;
    }

    /**
     * Returns the localization.
     *
     * @param string $locale
     *
     * @return array|null
     */
    public function getLocalization(string $locale): ?array
    {
        return $this->localizations[$locale] ?? null;
    }

    /**
     * Returns the localizations.
     *
     * @return array
     */
    public function getLocalizations(): array
    {
        return $this->localizations;
    }

    /**
     * Returns whether page should be locked
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Returns whether page has an advanced content
     *
     * @return bool
     */
    public function isAdvanced(): bool
    {
        return $this->advanced;
    }

    /**
     * Sets whether page has an advanced content
     *
     * @param bool $advanced
     *
     * @return RouteDefinition
     */
    public function setAdvanced(bool $advanced): RouteDefinition
    {
        $this->advanced = $advanced;

        return $this;
    }

    /**
     * Returns whether page has a dynamic path (with parameters).
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        return $this->dynamic;
    }

    /**
     * Sets whether page has a dynamic path (with parameters).
     *
     * @param bool $dynamic
     *
     * @return RouteDefinition
     */
    public function setDynamic(bool $dynamic): RouteDefinition
    {
        $this->dynamic = $dynamic;

        return $this;
    }

    /**
     * Returns the position
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Sets the position
     *
     * @param int
     *
     * @return RouteDefinition
     */
    public function setPosition(int $position): RouteDefinition
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Returns the seo.
     *
     * @return array
     */
    public function getSeo(): array
    {
        return $this->seo;
    }

    /**
     * Sets the seo.
     *
     * @param array $seo
     *
     * @return RouteDefinition
     */
    public function setSeo(array $seo): RouteDefinition
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Returns the menus.
     *
     * @return array
     */
    public function getMenus(): array
    {
        return $this->menus;
    }

    /**
     * Sets the menus.
     *
     * @param array $menus
     *
     * @return RouteDefinition
     */
    public function setMenus(array $menus): RouteDefinition
    {
        $this->menus = $menus;

        return $this;
    }

    /**
     * Adds a child route definition
     *
     * @param RouteDefinition $routeDefinition
     *
     * @return RouteDefinition
     */
    public function appendChild(RouteDefinition $routeDefinition): RouteDefinition
    {
        if ($routeDefinition->getPosition() == 0) {
            $routeDefinition->setPosition(count($this->children));
        }

        $seo = $routeDefinition->getSeo();
        if (!$this->seo['follow'] && $seo['follow']) {
            $seo['follow'] = false;
        }
        if (!$this->seo['index'] && $seo['index']) {
            $seo['index'] = false;
        }
        $routeDefinition->setSeo($seo);

        $routeName = $routeDefinition->getRouteName();
        if (array_key_exists($routeName, $this->children)) {
            throw new LogicException(sprintf('Route "%s" already exists.', $routeName));
        }

        $this->children[$routeName] = $routeDefinition;

        return $this;
    }

    /**
     * Returns children routes
     *
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Returns whether the definition has children definitions
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return 0 < count($this->children);
    }

    /**
     * Returns a child definition
     *
     * @param string $routeName
     *
     * @return RouteDefinition|null
     */
    public function findChildByRouteName(string $routeName): ?RouteDefinition
    {
        if ($this->hasChildren()) {
            if (array_key_exists($routeName, $this->children)) {
                return $this->children[$routeName];
            }

            /** @var RouteDefinition $definition */
            foreach ($this->children as $definition) {
                if (null !== $child = $definition->findChildByRouteName($routeName)) {
                    return $child;
                }
            }
        }

        return null;
    }

    /**
     * Sorts children definitions by position
     */
    public function sortChildren()
    {
        if ($this->hasChildren()) {
            /** @var RouteDefinition $definition */
            foreach ($this->children as $definition) {
                $definition->sortChildren();
            }

            uasort($this->children, function ($a, $b) {
                /** @var RouteDefinition $a */
                /** @var RouteDefinition $b */
                if ($a->getPosition() == $b->getPosition()) {
                    return 0;
                }

                return $a->getPosition() < $b->getPosition() ? -1 : 1;
            });
        }
    }
}
