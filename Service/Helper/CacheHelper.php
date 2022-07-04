<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Helper;

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
    private CacheItemPoolInterface $cmsCache;
    private ?AdapterInterface      $resultCache;
    private array                  $locales;
    private string                 $pageClass;

    public function __construct(
        CacheItemPoolInterface $cmsCache,
        ?AdapterInterface      $resultCache,
        array                  $locales,
        string                 $pageClass
    ) {
        $this->cmsCache = $cmsCache;
        $this->resultCache = $resultCache;
        $this->locales = $locales;
        $this->pageClass = $pageClass;
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

        foreach ($this->locales as $locale) {
            $this->resultCache->delete(
                call_user_func($this->pageClass . '::getRouteCacheTag', $page->getRoute(), $locale)
            );
        }
    }
}
