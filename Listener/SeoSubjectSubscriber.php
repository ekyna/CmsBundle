<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class SeoSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoSubjectSubscriber implements EventSubscriber
{
    const SEO_INTERFACE     = 'Ekyna\Bundle\CmsBundle\Model\SeoInterface';
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

        $metadata->mapOneToOne([
            'fieldName'     => 'seo',
            'targetEntity'  => self::SEO_INTERFACE,
            'cascade'       => ['all'],
//            'fetch' => ClassMetadataInfo::FETCH_EAGER,
            'orphanRemoval' => true,
            'joinColumns'   => [
                [
                    'name'                 => 'seo_id',
                    'referencedColumnName' => 'id',
                    'onDelete'             => 'RESTRICT',
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
