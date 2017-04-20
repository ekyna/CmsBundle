<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Serializer;

use Ekyna\Bundle\CmsBundle\Model;
use Ekyna\Component\Resource\Bridge\Symfony\Serializer\TranslatableNormalizer;

/**
 * Class PageNormalizer
 * @package Ekyna\Bundle\CmsBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SeoNormalizer extends TranslatableNormalizer
{
    /**
     * @inheritDoc
     *
     * @param Model\SeoInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return array_replace(
            ['id' => $object->getId()],
            $this->normalizeTranslations($object, $format, $context)
        );
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        //$resource = parent::denormalize($data, $class, $format, $context);

        throw new \Exception('Not yet implemented');
    }
}
