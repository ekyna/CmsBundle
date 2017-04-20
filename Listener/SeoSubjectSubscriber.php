<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;

/**
 * Class SeoSubjectSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SeoSubjectSubscriber
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        // Prevent doctrine:generate:entities bug
        if (!class_exists($metadata->getName())) {
            return;
        }

        // Check if class implements the subject interface
        if (!in_array(SeoSubjectInterface::class, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('seo')) {
            return;
        }

        $metadata->mapOneToOne([
            'fieldName'     => 'seo',
            'targetEntity'  => SeoInterface::class,
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
}
