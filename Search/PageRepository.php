<?php

namespace Ekyna\Bundle\CmsBundle\Search;

use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderAwareInterface;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderAwareTrait;
use Ekyna\Component\Resource\Search\Elastica\ResourceRepository;
use Ekyna\Bundle\CmsBundle\Search\Wide\ProviderInterface;
use Ekyna\Bundle\CmsBundle\Search\Wide\Result;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Search
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends ResourceRepository implements LocaleProviderAwareInterface, ProviderInterface
{
    use LocaleProviderAwareTrait;


    /**
     * @inheritdoc
     */
    public function search($expression, $limit = 10)
    {
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
    protected function getDefaultMatchFields()
    {
        $locale = $this->localeProvider->getCurrentLocale();

        return [
            'translations.'.$locale.'.title',
            'translations.'.$locale.'.html',
            'seo.translations.'.$locale.'.title',
            'seo.translations.'.$locale.'.description',
//            'content.'.$locale.'.content',
//            'title',
//            'html',
//            'seo.title',
//            'seo.description',
        ];
    }


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ekyna_cms_page';
    }
}
