<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Ekyna\Bundle\CmsBundle\Editor\PluginRegistry;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class AbstractBlockSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AbstractBlockSubscriber implements EventSubscriber
{
    const BLOCK_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\AbstractBlock';

    /**
     * @var PluginRegistry
     */
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
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() !== self::BLOCK_FQCN) {
            return;
        }

        foreach($this->registry->getPlugins() as $name => $plugins) {
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
