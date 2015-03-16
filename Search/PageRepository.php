<?php

namespace Ekyna\Bundle\CmsBundle\Search;

use Ekyna\Bundle\AdminBundle\Search\SearchRepositoryInterface;
use Elastica\Query;
use FOS\ElasticaBundle\Repository;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Search
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends Repository implements SearchRepositoryInterface
{
    public function defaultSearch($text)
    {
        if (0 == strlen($text)) {
            $query = new Query\MatchAll();
        } else {
            $query = new Query\MultiMatch();
            $query
                ->setQuery($text)
                ->setFields(array('title', 'seo.title', 'seo.description', 'content.content'))
            ;
        }

        return $this->find($query);
    }
}
