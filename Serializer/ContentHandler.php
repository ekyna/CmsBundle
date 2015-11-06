<?php

namespace Ekyna\Bundle\CmsBundle\Serializer;

use Ekyna\Bundle\CmsBundle\Entity\Content;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

/**
 * Class ContentHandler
 * @package Ekyna\Bundle\CmsBundle\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentHandler implements SubscribingHandlerInterface
{
    /**
     * Serializes a Content to Json.
     *
     * @param JsonSerializationVisitor $visitor
     * @param Content                  $content
     *
     * @return array
     */
    public function serializeContentToJson(JsonSerializationVisitor $visitor, Content $content) { /* array $type, Context $context */
        $result = array(
            'id' => $content->getId(),
        );
        /** @var \Ekyna\Bundle\CmsBundle\Model\BlockInterface $block */
        foreach ($content->getBlocks() as $block) {
            if ($block->isIndexable()) {
                foreach ($block->getIndexableContents() as $locale => $content) {
                    if (!array_key_exists($locale, $result)) {
                        $result[$locale] = array('content' => '');
                    }
                    $result[$locale]['content'] .= ' ' . $content;
                }
            }
        }

        if (null === $visitor->getRoot()) {
            $visitor->setRoot($result);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'Ekyna\Bundle\CmsBundle\Entity\Content',
                'method'    => 'serializeContentToJson',
            ),
        );
    }
}
