<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * ContentSubjectSubscriber.
 * 
 * @see http://www.theodo.fr/blog/2013/11/dynamic-mapping-in-doctrine-and-symfony-how-to-extend-entities/
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentSubjectSubscriber implements EventSubscriber
{
    const CONTENT_FQCN = 'Ekyna\Bundle\CmsBundle\Entity\Content';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface';

    protected $contentEnabled = false;

    /**
     * Constructor.
     * 
     * @param boolean $contentEnabled
     */
    public function __construct($contentEnabled)
    {
        $this->contentEnabled = (bool) $contentEnabled;
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        if(!$this->contentEnabled) {
            return;
        }

        $metadata = $eventArgs->getClassMetadata();

        if (!in_array(self::SUBJECT_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy()
        ;

        $metadata->mapManyToMany(array(
            'targetEntity'  => self::CONTENT_FQCN,
            'fieldName'     => 'contents',
            'cascade'       => array('persist'),
            'joinTable'     => array(
                'name'        => sprintf('cms_%s_content', strtolower($namingStrategy->classToTableName($metadata->getName()))),
                'joinColumns' => array(
                    array(
                        'name'                  => $namingStrategy->joinKeyColumnName($metadata->getName()),
                        'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                        'onDelete'  => 'CASCADE',
                        'onUpdate'  => 'CASCADE',
                    ),
                ),
                'inverseJoinColumns'    => array(
                    array(
                        'name'                  => 'content_id',
                        'referencedColumnName'  => $namingStrategy->referenceColumnName(),
                        'onDelete'  => 'CASCADE',
                        'onUpdate'  => 'CASCADE',
                    ),
                )
            )
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
