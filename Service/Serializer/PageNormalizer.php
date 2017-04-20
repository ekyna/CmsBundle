<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Model;
use Ekyna\Component\Resource\Bridge\Symfony\Serializer\TranslatableNormalizer;
use Exception;

/**
 * Class PageNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class PageNormalizer extends TranslatableNormalizer
{
    /**
     * @inheritDoc
     *
     * @param Model\PageInterface $page
     */
    public function normalize($page, $format = null, array $context = [])
    {
        $data = parent::normalize($page, $format, $context);

        if ($this->contextHasGroup(['Default', 'Page'], $context)) {
            // Seo
            if (null !== $seo = $page->getSeo()) {
                $data['seo'] = $seo->getId();
            }
        } elseif ($this->contextHasGroup('Search', $context)) {
            // Seo
            if (null !== $seo = $page->getSeo()) {
                $data['seo'] = $this->normalizeObject($seo, $format, $context);
            }

            // TODO content.[locale]
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        //$resource = parent::denormalize($data, $class, $format, $context);

        throw new Exception('Not yet implemented');
    }
}
