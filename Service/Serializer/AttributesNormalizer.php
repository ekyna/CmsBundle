<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Editor\View\AttributesInterface;
use Exception;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AttributesNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class AttributesNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @inheritDoc
     *
     * @param AttributesInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->toArray();
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        //$resource = parent::denormalize($data, $class, $format, $context);

        throw new Exception('Not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AttributesInterface;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return class_exists($type) && is_subclass_of($type, AttributesInterface::class);
    }
}
