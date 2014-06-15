<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Ekyna\Bundle\CmsBundle\Editor\PluginRegistry;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * BlockSubscriber.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockSubscriber implements EventSubscriber
{
    const BLOCK_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\AbstractBlock';

    protected $registry;

    /**
     * Constructor.
     * 
     * @param PluginRegistry $registry
     */
    public function __construct(PluginRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() !== self::BLOCK_FQCN) {
            return;
        }

        foreach($this->registry->getPlugins() as $name => $plugins) {
            /** @see \Doctrine\ORM\Mapping\ClassMetadataInfo::addDiscriminatorMapClass() */
            $metadata->addDiscriminatorMapClass($name, $plugins->getClass());
        }
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
