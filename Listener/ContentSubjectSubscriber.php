<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class ContentSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentSubjectSubscriber implements EventSubscriber
{
    const CONTENT_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\Content';
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
            ->getNamingStrategy()
        ;

        $metadata->mapOneToOne(array(
            'fieldName'     => 'content',
            'targetEntity'  => self::CONTENT_FQCN,
            'cascade'       => array('all'),
            'joinColumn' => array(
                array(
                    'name'                  => $namingStrategy->joinKeyColumnName($metadata->getName()),
                    'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                    'onDelete'              => 'CASCADE',
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }
}
