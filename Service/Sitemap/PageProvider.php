<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Sitemap;

use DateTimeInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Ekyna\Bundle\SitemapBundle\Provider\AbstractProvider;
use Ekyna\Bundle\SitemapBundle\Url\Url;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PageProvider
 * @package Ekyna\Bundle\CmsBundle\Service\Sitemap
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageProvider extends AbstractProvider
{
    private PageRepositoryInterface $repository;
    private UrlGeneratorInterface   $urlGenerator;

    public function __construct(PageRepositoryInterface $repository, UrlGeneratorInterface $urlGenerator)
    {
        $this->repository = $repository;
        $this->urlGenerator = $urlGenerator;
    }

    public function getLastUpdateDate(): ?DateTimeInterface
    {
        return $this->repository->getLastUpdatedAt();
    }

    public function getUrls(): array
    {
        $pages = $this->repository->getIndexablePages();

        $entries = [];
        foreach ($pages as $page) {
            try {
                $location = $this->urlGenerator->generate($page->getRoute(), [], UrlGeneratorInterface::ABSOLUTE_URL);
            } catch (Exception $e) {
                continue;
            }

            $entry = new Url();
            $entry
                ->setLocation($location)
                ->setLastmod($page->getUpdatedAt())
                ->setChangefreq($page->getSeo()->getChangefreq())
                ->setPriority($page->getSeo()->getPriority());
            $entries[] = $entry;
        }

        return $entries;
    }

    public function getSitemap(): string
    {
        return 'pages';
    }

    public function getName(): string
    {
        return 'ekyna_cms';
    }
}
