<?php

namespace Ekyna\Bundle\CmsBundle\Sitemap;

use Doctrine\ORM\Query;
use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\SitemapBundle\Url\Url;
use Ekyna\Bundle\SitemapBundle\Provider\AbstractProvider;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PageProvider
 * @package Ekyna\Bundle\CmsBundle\Sitemap
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageProvider extends AbstractProvider
{
    /**
     * @var PageRepository
     */
    private $repository;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param PageRepository $repository
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(PageRepository $repository, UrlGeneratorInterface $urlGenerator)
    {
        $this->repository = $repository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastUpdateDate()
    {
        $row = $this->repository->createQueryBuilder('p')
            ->select('MAX(p.updatedAt) AS last_date')
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY)
        ;

        return new \DateTime($row['last_date']);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls()
    {
        $qb = $this->repository->createQueryBuilder('p');
        $pages = $qb
            ->innerJoin('p.seo', 's')
            ->andWhere($qb->expr()->eq('s.index', true))
            ->orderBy('p.left', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $entries = [];
        /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
        foreach($pages as $page) {
            try {
                $location = $this->urlGenerator->generate($page->getRoute(), array(), UrlGeneratorInterface::ABSOLUTE_URL);
            } catch(\Exception $e) {
                continue;
            }

            $entry = new Url();
            $entry
                ->setLocation($location)
                ->setLastmod($page->getUpdatedAt())
                ->setChangefreq($page->getSeo()->getChangefreq())
                ->setPriority($page->getSeo()->getPriority())
            ;
            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * {@inheritdoc}
     */
    public function getSitemap()
    {
        return 'pages';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms';
    }
} 