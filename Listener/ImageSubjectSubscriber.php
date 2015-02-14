<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class ImageSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageSubjectSubscriber implements EventSubscriber
{
    const IMAGE_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\Image';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\ImageSubjectInterface';

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
        if ($metadata->hasAssociation('image')) {
            return;
        }

        $metadata->mapManyToOne(array(
            'fieldName'     => 'image',
            'targetEntity'  => self::IMAGE_FQCN,
            'cascade'       => array('persist', 'refresh', 'detach', 'merge'),
            'joinColumns' => array(
                array(
                    'name'                  => 'image_id',
                    'referencedColumnName'  => 'id',
                    'onDelete'              => 'RESTRICT',
                    'nullable'              => true,
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
