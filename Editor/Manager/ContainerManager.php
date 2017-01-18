<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model;
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
     * Changes the container type.
     *
     * @param Model\ContainerInterface $container The container
     * @param string                   $type      The container new type
     * @param array                    $data      The container new data
     *
     * @throws InvalidOperationException
     */
    public function changeType(Model\ContainerInterface $container, $type, array $data = [])
    {
        if ($type === $container->getType()) {
            return;
        }

        if ($container->isNamed()) {
            throw new InvalidOperationException(
                "The type of this container can't be changed."
            );
        }

        // Plugin removal
        $this->editor
            ->getContainerPlugin($container->getType())
            ->remove($container);

        // Sets the new type
        $container->setType($type);

        // Plugin creation
        $this->editor
            ->getContainerPlugin($container->getType())
            ->create($container, $data);
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
        // Ensure not named / alone
        if ($container->isAlone() || $container->isNamed()) {
            throw new InvalidOperationException(
                "The container can't be removed because it is named or the parent content does not have enough children."
            );
        }

        // Check if the container is not the only content container (a content must have at least one container).
        if (null === $content = $container->getContent()) {
            throw new InvalidOperationException(
                "The container does not belong to a content and therefore can't be removed."
            );
        }

        // Plugin remove
        $this->editor
            ->getContainerPlugin($container->getType())
            ->remove($container);

        $content->removeContainer($container);

        $this->editor
            ->getContentManager()
            ->fixContainersPositions($content);

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
        $sibling = $this->editor->getRepository()->findSiblingContainer($container, false);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The container can't be moved up as no sibling container has been found."
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
        $sibling = $this->editor->getRepository()->findSiblingContainer($container, true);
        if (null === $sibling) {
            throw new InvalidOperationException(
                "The container can't be moved down as no sibling container has been found."
            );
        }

        $container->setPosition($container->getPosition() + 1);
        $sibling->setPosition($sibling->getPosition() - 1);

        $this->sortChildrenByPosition($container->getContent(), 'containers');

        return $sibling;
    }

    /**
     * Fix the rows positions.
     *
     * @param Model\ContainerInterface $container
     *
     * @return ContainerManager
     */
    public function fixRowsPositions(Model\ContainerInterface $container)
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
}
