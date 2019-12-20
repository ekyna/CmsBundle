<?php

namespace Ekyna\Bundle\CmsBundle\Search;

use Ekyna\Bundle\CmsBundle\Search\Wide\ProviderInterface;
use Ekyna\Bundle\CmsBundle\Search\Wide\Result;
use Ekyna\Component\Resource\Locale;
use Ekyna\Component\Resource\Search\Elastica\ResourceRepository;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Search
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends ResourceRepository implements Locale\LocaleProviderAwareInterface, ProviderInterface
{
    use Locale\LocaleProviderAwareTrait;


    /**
     * @inheritdoc
     */
    public function search(string $expression, int $limit = 10): array
    {
        if (empty($expression)) {
            return [];
        }

        $results = [];
        /** @var \FOS\ElasticaBundle\HybridResult[] $elasticaResults */
        $elasticaResults = $this->findHybrid($this->createMatchQuery($expression), $limit);

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
     * @inheritDoc
     */
    protected function getDefaultMatchFields(): array
    {
        $locale = $this->localeProvider->getCurrentLocale();

        return [
            'translations.' . $locale . '.title',
            'translations.' . $locale . '.title.analyzed',
            'translations.' . $locale . '.html',
            'translations.' . $locale . '.html.analyzed',
            'seo.translations.' . $locale . '.title',
            'seo.translations.' . $locale . '.title.analyzed',
            'seo.translations.' . $locale . '.description',
            'seo.translations.' . $locale . '.description.analyzed',
            //            'content.'.$locale.'.content',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'ekyna_cms_page';
    }
}
