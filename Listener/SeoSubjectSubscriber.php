<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class SeoSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoSubjectSubscriber implements EventSubscriber
{
    const SEO_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\Seo';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface';

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
        if ($metadata->hasAssociation('seo')) {
            return;
        }

        $metadata->mapOneToOne(array(
            'fieldName'     => 'seo',
            'targetEntity'  => self::SEO_FQCN,
            'cascade'       => array('all'),
//            'fetch' => ClassMetadataInfo::FETCH_EAGER,
            'orphanRemoval' => true,
            'joinColumns' => array(
                array(
                    'name'                  => 'seo_id',
                    'referencedColumnName'  => 'id',
                    'onDelete'              => 'RESTRICT',
                    'nullable'              => false,
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
