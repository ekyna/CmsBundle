<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistry;
use Ekyna\Bundle\CmsBundle\Model;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContainerManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerManager extends AbstractManager
{
    /**
     * @var string
     */
    private $defaultType;


    /**
     * Constructor.
     *
     * @param string $defaultType
     */
    public function __construct($defaultType)
    {
        $this->defaultType = $defaultType;
    }

    /**
     * Creates a new container.
     *
     * @param Model\ContentInterface|string $contentOrName
     * @param string                        $type
     * @param array                         $data
     *
     * @return Model\ContainerInterface
     * @throws InvalidOperationException
     */
    public function create($contentOrName, $type = null, array $data = [])
    {
        // Check if container or name is defined
        if (!(
            $contentOrName instanceof Model\ContentInterface ||
            (is_string($contentOrName) && 0 < strlen($contentOrName))
        )
        ) {
            throw new InvalidOperationException("Excepted instance of ContentInterface or string.");
        }

        // Default type if null
        if (null === $type) {
            $type = $this->defaultType;
        }

        // New instance
        $container = $this->editor->getRepository()->createContainer();
        $container->setType($type);

        // Plugin creation
        $this->editor
            ->getContainerPlugin($type)
            ->create($container, $data);

        // Create default row
        $this->editor->getRowManager()->create($container);

        // Add to container if available
        if ($contentOrName instanceof Model\ContentInterface) {
            $count = $contentOrName->getContainers()->count();
            $container->setPosition($count);
            $contentOrName->addContainer($container);
        } else {
            $container->setName($contentOrName);
        }

        return $container;
    }

    /**
     * Updates the container.
     *
     * @param Model\ContainerInterface $container
     * @param Request                  $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @throws InvalidOperationException
     */
    public function update(Model\ContainerInterface $container, Request $request)
    {
        // Plugin update
        return $this->editor
            ->getContainerPlugin($container->getType())
            ->update($container, $request);
    }

    /**
     * Deletes the container.
     *
     * @param Model\ContainerInterface $container
     *
     * @return Model\ContainerInterface The removed container.
     * @throws InvalidOperationException
     */
    public function delete(Model\ContainerInterface $container)
    {
        // Check if the container is not the only content container (a content must have at least one container).
        $content = $container->getContent();
        if (null === $content) {
            throw new InvalidOperationException(
                "The container does not belong to a content and therefore can't be removed."
            );
        }

        $containers = $container->getContent()->getContainers();
        if (1 >= $containers->count()) {
            throw new InvalidOperationException(
                "The container can't be removed because the parent content does not have enough children."
            );
        }

        // Plugin remove
        $this->editor
            ->getContainerPlugin($container->getType())
            ->remove($container);

        $containers->removeElement($container);

        $this->editor
            ->getContentManager()
            ->fixContainerPositions($content);

        return $container;
    }

    /**
     * Moves the container up.
     *
     * @param Model\ContainerInterface $container
     *
     * @return Model\ContainerInterface the sibling container that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveUp(Model\ContainerInterface $container)
    {
        $sibling = $this->findPreviousSibling($container);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The container can't be moved up as no previous sibling has been found."
            );
        }

        $container->setPosition($container->getPosition() - 1);
        $sibling->setPosition($sibling->getPosition() + 1);

        $this->sortChildrenByPosition($container->getContent(), 'containers');

        return $sibling;
    }

    /**
     * Moves the container down.
     *
     * @param Model\ContainerInterface $container
     *
     * @return Model\ContainerInterface the sibling container that has been swapped.
     * @throws InvalidOperationException
     */
    public function moveDown(Model\ContainerInterface $container)
    {
        $sibling = $this->findNextSibling($container);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The container can't be moved down as no next sibling has been found."
            );
        }

        $container->setPosition($container->getPosition() + 1);
        $sibling->setPosition($sibling->getPosition() - 1);

        $this->sortChildrenByPosition($container->getContent(), 'containers');

        return $sibling;
    }

    /**
     * Fix the row positions.
     *
     * @param Model\ContainerInterface $container
     *
     * @return ContainerManager
     */
    public function fixRowPositions(Model\ContainerInterface $container)
    {
        $this->sortChildrenByPosition($container, 'rows');

        $rows = $container->getRows();

        $position = 0;
        foreach ($rows as $row) {
            $row->setPosition($position);
            $position++;
        }

        return $this;
    }

    /**
     * The container's previous sibling.
     *
     * @param Model\ContainerInterface $container
     *
     * @return Model\ContainerInterface|null
     * @throws InvalidOperationException
     */
    private function findPreviousSibling(Model\ContainerInterface $container)
    {
        if (null === $content = $container->getContent()) {
            throw new InvalidOperationException('The container does not have a parent content.');
        }

        // Return null if this is the first container
        if (0 == $container->getPosition()) {
            return null;
        }

        $containers = $content->getContainers();

        $sibling = $containers->filter(function (Model\ContainerInterface $b) use ($container) {
            return $b->getPosition() == $container->getPosition() - 1;
        })->first();

        return $sibling ? $sibling : null;
    }

    /**
     * Finds the container's next sibling.
     *
     * @param Model\ContainerInterface $container
     *
     * @return Model\ContainerInterface|null
     * @throws InvalidOperationException
     */
    private function findNextSibling(Model\ContainerInterface $container)
    {
        if (null === $content = $container->getContent()) {
            throw new InvalidOperationException('The container does not have a parent content.');
        }

        $containers = $content->getContainers();

        // Return null if this is the last container
        if ($containers->count() - 1 == $container->getPosition()) {
            return null;
        }

        $sibling = $containers->filter(function (Model\ContainerInterface $b) use ($container) {
            return $b->getPosition() == $container->getPosition() + 1;
        })->first();

        return $sibling ? $sibling : null;
    }
}
