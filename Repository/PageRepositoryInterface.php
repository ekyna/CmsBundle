<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use DateTime;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Interface PageRepositoryInterface
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method PageInterface|null find(int $id)
 * @method PageInterface|null findOneBy(array $criteria, array $sorting = [])
 * @method PageInterface[] findAll()
 * @method PageInterface[] findBy(array $criteria, array $sorting = [], int $limit = null, int $offset = null)
 */
interface PageRepositoryInterface extends TranslatableRepositoryInterface
{
    /**
     * Returns the last updated at date time.
     *
     * @return DateTime|null
     */
    public function getLastUpdatedAt(): ?DateTime;

    /**
     * Finds a page by request.
     *
     * @param string $routeName
     * @param bool   $cached
     *
     * @return PageInterface|null
     */
    public function findOneByRoute(string $routeName, bool $cached = false): ?PageInterface;

    /**
     * Finds the parents pages (including the given one) for breadcrumb.
     *
     * @param PageInterface $current
     *
     * @return array
     */
    public function findParentsForBreadcrumb(PageInterface $current): array;

    /**
     * Returns the pages routes.
     *
     * @return array
     */
    public function getPagesRoutes(): array;

    /**
     * Returns the indexable pages.
     *
     * @return array<PageInterface>
     */
    public function getIndexablePages(): array;

    /**
     * Returns the routes data matching the given path.
     *
     * @param string $path
     *
     * @return array
     */
    public function getRoutesDataByPath(string $path): array;

    /**
     * Returns the routes data matching the given names.
     *
     * @param array|null $names
     *
     * @return array
     */
    public function getRoutesDataByNames(?array $names): array;

    /**
     * Returns the route data matching the given name.
     *
     * @param string $name
     *
     * @return array|null
     */
    public function getRouteDataByName(string $name): ?array;
}
