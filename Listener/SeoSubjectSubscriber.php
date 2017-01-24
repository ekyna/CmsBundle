<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class SeoSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoSubjectSubscriber implements EventSubscriber
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
        if (!in_array(Model\SeoSubjectInterface::class, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('seo')) {
            return;
        }

        $metadata->mapOneToOne([
            'fieldName'     => 'seo',
            'targetEntity'  => Model\SeoInterface::class,
            'cascade'       => ['persist', 'detach', 'remove'],
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
