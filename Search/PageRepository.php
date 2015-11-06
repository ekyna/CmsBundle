<?php

namespace Ekyna\Bundle\CmsBundle\Search;

use Ekyna\Bundle\AdminBundle\Search\SearchRepositoryInterface;
use Ekyna\Bundle\CmsBundle\Search\Wide\ProviderInterface;
use Ekyna\Bundle\CmsBundle\Search\Wide\Result;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;
use Elastica\Query;
use FOS\ElasticaBundle\Repository;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Search
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends Repository implements SearchRepositoryInterface, ProviderInterface
{
    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * Sets the localeProvider.
     *
     * @param LocaleProviderInterface $localeProvider
     */
    public function setLocaleProvider($localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     * @return \Ekyna\Bundle\CmsBundle\Model\PageInterface[]
     */
    public function defaultSearch($expression, $limit = 10)
    {
        return $this->find($this->createQuery($expression), $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function search($expression, $limit = 10)
    {
        $results = [];
        /** @var \FOS\ElasticaBundle\HybridResult[] $elasticaResults */
        $elasticaResults = $this->findHybrid($this->createQuery($expression), $limit);

        foreach ($elasticaResults as $elasticaResult) {
            /** @var \Ekyna\Bundle\CmsBundle\Model\PageInterface $page */
            $page = $elasticaResult->getTransformed();

            $result = new Result();
            $result
                ->setTitle($page->getTitle())
                ->setDescription($page->getSeo()->getDescription())
                ->setRoute($page->getRoute())
                ->setScore($elasticaResult->getResult()->getScore());

            $results[] = $result;
        }

        return $results;
    }

    /**
     * Creates the query.
     *
     * @param string $expression
     *
     * @return Query\AbstractQuery
     */
    private function createQuery($expression)
    {
        if (0 == strlen($expression)) {
            return new Query\MatchAll();
        }

        $locale = $this->localeProvider->getCurrentLocale();

        $query = new Query\MultiMatch();
        $query
            ->setQuery($expression)
            ->setFields(array(
                'translations.'.$locale.'.title',
                'translations.'.$locale.'.html',
                'seo.translations.'.$locale.'.title',
                'seo.translations.'.$locale.'.description',
                'content.'.$locale.'.content',
            ));

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_page';
    }
}
