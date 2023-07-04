<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Helper;

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Service\Routing\RouteProvider;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

use function call_user_func;

/**
 * Class CacheHelper
 * @package Ekyna\Bundle\CmsBundle\Service\Helper
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CacheHelper
{
    public function __construct(
        private readonly CacheItemPoolInterface $cmsCache,
        private readonly ?AdapterInterface      $resultCache,
        private readonly array                  $locales,
        private readonly string                 $pageClass
    ) {
    }

    /**
     * Purges the pages routes cache.
     */
    public function purgeRoutesCache(): void
    {
        $this->cmsCache->deleteItem(PageHelper::CACHE_KEY);
        $this->cmsCache->deleteItem(RouteProvider::CACHE_KEY);
    }

    /**
     * Purges the page cache.
     */
    public function purgePageCache(PageInterface $page): void
    {
        if (!$this->resultCache) {
            return;
        }

        /** @see \Ekyna\Bundle\CmsBundle\Entity\Page::getRouteCacheTag */
        $cache = DoctrineProvider::wrap($this->resultCache);

        foreach ($this->locales as $locale) {
            $id = call_user_func($this->pageClass . '::getRouteCacheTag', $page->getRoute(), $locale);

            $cache->delete($id);
        }
    }
}
