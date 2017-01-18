<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class ContentSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ContentSubjectSubscriber implements EventSubscriber
{
    const CONTENT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface';

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        // Prevent doctrine:generate:entities command bug
        if (!class_exists($metadata->getName())) {
            return;
        }

        // Check if class implements the subject interface
        if (!in_array(self::SUBJECT_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('content')) {
            return;
        }

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy();

        $metadata->mapOneToOne([
            'fieldName'     => 'content',
            'targetEntity'  => self::CONTENT_INTERFACE,
            'cascade'       => ['all'],
            'orphanRemoval' => true,
            'joinColumn'    => [
                [
                    'name'                 => $namingStrategy->joinKeyColumnName($metadata->getName()),
                    'referencedColumnName' => $namingStrategy->referenceColumnName(),
                    'onDelete'             => 'CASCADE',
                    'nullable'             => true,
                ],
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
