<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Component\Resource\Model AS RM;

/**
 * Interface ContentInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentInterface extends RM\TimestampableInterface, RM\TaggedEntityInterface
{
    /**
     * Sets the name
     *
     * @param string $name
     * @return ContentInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName();

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
     * Returns whether or not the content is named.
     *
     * @return boolean
     */
    public function isNamed();
}
