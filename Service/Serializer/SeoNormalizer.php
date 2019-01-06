<?php

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Model;
use Ekyna\Component\Resource\Serializer\AbstractTranslatableNormalizer;

/**
 * Class PageNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SeoNormalizer extends AbstractTranslatableNormalizer
{
    /**
     * @inheritdoc
     *
     * @param Model\SeoInterface $resource
     */
    public function normalize($resource, $format = null, array $context = [])
    {
        return array_replace(
            ['id' => $resource->getId()],
            $this->normalizeTranslations($resource, $format, $context)
        );
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
        return $data instanceof Model\SeoInterface;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_subclass_of($type, Model\SeoInterface::class);
    }
}
