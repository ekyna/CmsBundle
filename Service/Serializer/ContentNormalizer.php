<?php

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface;
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
     *
     * @param ContentInterface $content
     */
    public function normalize($content, $format = null, array $context = [])
    {
        $data = parent::normalize($content, $format, $context);

        if ($this->contextHasGroup(['Search', 'Content'], $context)) {
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
        //$resource = parent::denormalize($data, $class, $format, $context);

        throw new \Exception('Not yet implemented');
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ContentInterface;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_subclass_of($type, ContentInterface::class);
    }
}
