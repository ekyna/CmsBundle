<?php

namespace Ekyna\Bundle\CmsBundle\Service\Search;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Component\Resource\Exception\RuntimeException;
use Ekyna\Component\Resource\Locale;
use Ekyna\Component\Resource\Search\Elastica\ResourceRepository;
use Ekyna\Component\Resource\Search\Request;
use Ekyna\Component\Resource\Search\Result;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Search
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends ResourceRepository implements Locale\LocaleProviderAwareInterface
{
    use Locale\LocaleProviderAwareTrait;


    /**
     * @inheritDoc
     */
    protected function createResult($source, Request $request): ?Result
    {
        if (!$source instanceof PageInterface) {
            throw new RuntimeException("Expected instance of " . PageInterface::class);
        }

        $result = new Result();
        $result->setTitle($source->getTitle());

        if ($request->isPrivate()) {
            $result
                ->setRoute('ekyna_cms_page_admin_show')
                ->setParameters(['pageId' => $source->getId()]);

            return $result;
        }

        if ($source->isDynamicPath()) {
            return null;
        }

        return $result
            ->setRoute($source->getRoute())
            ->setDescription($source->getSeo()->getDescription());
    }

    /**
     * @inheritDoc
     */
    protected function needsTransformedSource(Request $request): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool
    {
        return !empty($request->getExpression()) && 0 < $request->getLimit();
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFields(): array
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
            // 'content.'.$locale.'.content',
        ];
    }
}
