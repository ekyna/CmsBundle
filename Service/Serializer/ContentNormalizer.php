<?php

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Model;
use Ekyna\Component\Resource\Serializer\AbstractTranslatableNormalizer;

/**
 * Class ContentNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentNormalizer extends AbstractTranslatableNormalizer
{
    /**
     * @inheritdoc
     */
    public function normalize($content, $format = null, array $context = [])
    {
        $data = parent::normalize($content, $format, $context);

        /** @var Model\ContentInterface $content */

        $groups = isset($context['groups']) ? (array)$context['groups'] : [];

        if (in_array('Search', $groups)) {
            // TODO localized block's text
            // $data[$locale] = ...
        }

        return $data;
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
        return $data instanceof Model\ContentInterface;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_subclass_of($type, Model\ContentInterface::class);
    }
}
