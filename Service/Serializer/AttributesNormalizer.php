<?php

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Editor\View\AttributesInterface;
use Ekyna\Component\Resource\Serializer\AbstractTranslatableNormalizer;

/**
 * Class AttributesNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class AttributesNormalizer extends AbstractTranslatableNormalizer
{
    /**
     * @inheritdoc
     *
     * @param AttributesInterface $attributes
     */
    public function normalize($attributes, $format = null, array $context = [])
    {
        return $attributes->toArray();
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
        return $data instanceof AttributesInterface;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_subclass_of($type, AttributesInterface::class);
    }
}
