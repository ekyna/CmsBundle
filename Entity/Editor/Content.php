<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Component\Resource\Copier\CopierInterface;
use Ekyna\Component\Resource\Model as RM;
use Ekyna\Component\Resource\Model\AbstractResource;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Content extends AbstractResource implements EM\ContentInterface
{
    use RM\TaggedEntityTrait;
    use RM\TimestampableTrait;

    protected ?string    $name = null;
    protected Collection $containers;

    public function __construct()
    {
        $this->containers = new ArrayCollection();
    }

    public function onCopy(CopierInterface $copier): void
    {
        $copier->copyCollection($this, 'containers', true);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name = null): EM\ContentInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setContainers(Collection $containers): EM\ContentInterface
    {
        foreach ($containers as $container) {
            $this->addContainer($container);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addContainer(EM\ContainerInterface $container): EM\ContentInterface
    {
        $container->setContent($this);
        $this->containers->add($container);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeContainer(EM\ContainerInterface $container): EM\ContentInterface
    {
        $container->setContent();
        $this->containers->removeElement($container);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContainers(): Collection
    {
        return $this->containers;
    }

    /**
     * @inheritDoc
     */
    public function isNamed(): bool
    {
        return !empty($this->name);
    }

    /**
     * @inheritDoc
     */
    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_cms.content';
    }
}
