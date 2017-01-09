<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\EditorAwareInterface;
use Ekyna\Bundle\CmsBundle\Editor\EditorAwareTrait;
use Ekyna\Component\Resource\Model\SortableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class AbstractManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractManager implements EditorAwareInterface
{
    use EditorAwareTrait;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;


    /**
     * Returns the property accessor.
     *
     * @return \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private function getPropertyAccessor()
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }

    /**
     * Sorts the entity children by position.
     *
     * @param object $parent
     * @param string $childrenPropertyPath
     *
     * @see http://stackoverflow.com/a/22183527
     */
    protected function sortChildrenByPosition($parent, $childrenPropertyPath)
    {
        $collection = $this->getPropertyAccessor()->getValue($parent, $childrenPropertyPath);

        if ($collection instanceOf PersistentCollection) {
            /** @var PersistentCollection $collection */
            if (false === $collection->isInitialized()) {
                $collection->initialize();
            }
            $collection = $collection->unwrap();
        }

        if (!$collection instanceOf ArrayCollection) {
            throw new \InvalidArgumentException('Expected ArrayCollection.');
        }

        /** @var ArrayCollection $collection */
        $iterator = $collection->getIterator();
        $iterator->uasort(function (SortableInterface $a, SortableInterface $b) {
            return
                ($a->getPosition() == $b->getPosition()) ? 0 :
                    ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        $collection->clear();
        foreach ($iterator as $key => $item) {
            $collection->set($key, $item);
        }
    }
}
