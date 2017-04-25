<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class TagsSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TagsSubjectSubscriber implements EventSubscriber
{
    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        // Prevent doctrine:generate:entities bug
        if (!class_exists($metadata->getName())) {
            return;
        }

        // Check if class implements the subject interface
        if (!in_array(Model\TagsSubjectInterface::class, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('tags')) {
            return;
        }

        $metadata->mapManyToMany([
            'fieldName'    => 'tags',
            'targetEntity' => Model\TagInterface::class,
            'joinTable'    => [
                'name' => $metadata->getTableName() . '_tags',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }
}
