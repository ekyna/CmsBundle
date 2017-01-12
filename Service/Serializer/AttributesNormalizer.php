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
     */
    public function normalize($attributes, $format = null, array $context = [])
    {
        /** @var AttributesInterface $attributes */

        return $attributes->toArray();

        /*$result = [];

        if (null !== $id = $attributes->getId()) {
            $result['id'] = $id;
        }
        if (!empty($classes = $attributes->getClasses())) {
            $result['class'] = implode(' ', $classes);
        }
        if (!empty($styles = $attributes->getClasses())) {
            $result['style'] = implode(';', array_map(function($key, $value) {
                return "$key:$value";
            }, array_keys($styles), array_values($styles)));
        }
        if (!empty($data = $attributes->getData())) {
            $result['data-cms'] = json_encode($data);
        }
        if (!empty($extra = $attributes->getExtra())) {
            foreach ($extra as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;*/
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $resource = parent::denormalize($data, $class, $format, $context);

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
