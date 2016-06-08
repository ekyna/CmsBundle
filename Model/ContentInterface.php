<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model;

/**
 * Interface ContentInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentInterface extends Model\TimestampableInterface, Model\TaggedEntityInterface
{
    /**
     * Set containers
     *
     * @param ArrayCollection|ContainerInterface[] $containers
     *
     * @return ContentInterface|$this
     */
    public function setContainers(ArrayCollection $containers);

    /**
     * Add container
     *
     * @param ContainerInterface $container
     *
     * @return ContentInterface|$this
     */
    public function addContainer(ContainerInterface $container);

    /**
     * Remove containers
     *
     * @param ContainerInterface $container
     *
     * @return ContentInterface|$this
     */
    public function removeContainer(ContainerInterface $container);

    /**
     * Get containers
     *
     * @return ArrayCollection|ContainerInterface[]
     */
    public function getContainers();

    /**
     * Sorts the containers by position.
     *
     * @return ContentInterface|$this
     */
    public function sortContainers();

    /**
     * Returns the indexable contents indexed by locale.
     *
     * @return array
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents();
}
