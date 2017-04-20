<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface;
use Ekyna\Component\Resource\Bridge\Symfony\Serializer\TranslatableNormalizer;
use Exception;

/**
 * Class ContentNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentNormalizer extends TranslatableNormalizer
{
    /**
     * @inheritDoc
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
        return $data instanceof ContentInterface;
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return class_exists($type) && is_subclass_of($type, ContentInterface::class);
    }
}
