<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class TagsSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TagsSubjectSubscriber
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
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
                'name'               => $metadata->getTableName() . '_tags',
                'inverseJoinColumns' => [
                    [
                        'name'                 => 'tag_id',
                        'referencedColumnName' => 'id',
                        'onDelete'             => 'CASCADE',
                    ],
                ],
            ],
        ]);
    }
}
