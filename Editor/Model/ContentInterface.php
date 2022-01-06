<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Copier\CopyInterface;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface ContentInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentInterface extends RM\TimestampableInterface, CopyInterface, RM\TaggedEntityInterface
{
    /**
     * Sets the name
     *
     * @param string|null $name
     *
     * @return ContentInterface|$this
     */
    public function setName(string $name = null): ContentInterface;

    /**
     * Returns the name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set containers
     *
     * @param Collection|ContainerInterface[] $containers
     *
     * @return ContentInterface|$this
     */
    public function setContainers(Collection $containers): ContentInterface;

    /**
     * Add container
     *
     * @param ContainerInterface $container
     *
     * @return ContentInterface|$this
     */
    public function addContainer(ContainerInterface $container): ContentInterface;

    /**
     * Remove containers
     *
     * @param ContainerInterface $container
     *
     * @return ContentInterface|$this
     */
    public function removeContainer(ContainerInterface $container): ContentInterface;

    /**
     * Get containers
     *
     * @return Collection|ContainerInterface[]
     */
    public function getContainers(): Collection;

    /**
     * Returns whether or not the content is named.
     *
     * @return bool
     */
    public function isNamed(): bool;
}
