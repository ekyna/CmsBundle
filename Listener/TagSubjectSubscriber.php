<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class TagSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TagSubjectSubscriber implements EventSubscriber
{
    const TAG_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\Tag';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\TagSubjectInterface';

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

        if (!in_array(self::SUBJECT_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy()
        ;

        $metadata->mapManyToMany(array(
            'fieldName'     => 'tags',
            'targetEntity'  => self::TAG_FQCN,
            'cascade'       => array('persist'),
            'joinTable'     => array(
                'name'        => sprintf('%s_tag', strtolower($metadata->getTableName())),
                'joinColumns' => array(
                    array(
                        'name'                  => $namingStrategy->joinKeyColumnName($metadata->getName()),
                        'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                        'onDelete'              => 'CASCADE',
                    ),
                ),
                'inverseJoinColumns'    => array(
                    array(
                        'name'                  => 'tag_id',
                        'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                        'onDelete'              => 'CASCADE',
                    ),
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