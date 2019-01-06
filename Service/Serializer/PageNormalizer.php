<?php

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Model;
use Ekyna\Component\Resource\Serializer\AbstractTranslatableNormalizer;

/**
 * Class PageNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class PageNormalizer extends AbstractTranslatableNormalizer
{
    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        //$resource = parent::denormalize($data, $class, $format, $context);

        throw new \Exception('Not yet implemented');
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Model\PageInterface;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_subclass_of($type, Model\PageInterface::class);
    }
}
