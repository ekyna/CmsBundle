<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class FileSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class FileSubjectSubscriber implements EventSubscriber
{
    const FILE_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\File';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\FileSubjectInterface';

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
        if (!in_array(self::SUBJECT_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('file')) {
            return;
        }

        $metadata->mapManyToOne(array(
            'fieldName'     => 'file',
            'targetEntity'  => self::FILE_FQCN,
            'cascade'       => array('persist', 'refresh', 'detach', 'merge'),
            //'orphanRemoval' => true,
            'joinColumns' => array(
                array(
                    'name'                  => 'file_id',
                    'referencedColumnName'  => 'id',
                    'onDelete'              => 'RESTRICT',
                    'nullable'              => true,
                ),
            ),
            // TODO fetch => ?
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
