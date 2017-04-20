<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Container\CopyPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContainerManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerManager extends AbstractManager
{
    private string $defaultType;


    /**
     * Constructor.
     *
     * @param string $defaultType
     */
    public function __construct(string $defaultType)
    {
        $this->defaultType = $defaultType;
    }

    /**
     * Creates a new container.
     *
     * @param Model\ContentInterface|string $contentOrName
     * @param string|null                   $type
     * @param array                         $data
     *
     * @return Model\ContainerInterface
     * @throws InvalidOperationException
     */
    public function create($contentOrName, string $type = null, array $data = []): Model\ContainerInterface
    {
        // Check if container or name is defined
        if (
            !$contentOrName instanceof Model\ContentInterface &&
            !(is_string($contentOrName) && !empty($contentOrName))
        ) {
            throw new InvalidOperationException('Excepted instance of ContentInterface or string.');
        }

        // Default type if null
        if (null === $type) {
            $type = $this->defaultType;
        }

        // New instance
        $container = $this->editor->getRepository()->createContainer();
        $container->setType($type);

        // Plugin creation
        $plugin = $this->editor->getContainerPlugin($type);
        $plugin->create($container, $data);

        // Create default row
        if (!$plugin instanceof CopyPlugin) {
            $this->editor->getRowManager()->create($container);
        }

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
     * @return Response|null
     */
    public function update(Model\ContainerInterface $container, Request $request): ?Response
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
     * @return string[] The removed elements
     *
     * @throws InvalidOperationException
     */
    public function changeType(Model\ContainerInterface $container, string $type, array $data = []): array
    {
        if ($type === $container->getType()) {
            return [];
        }

        if ($container->isNamed() || $container->isTitled()) {
            throw new InvalidOperationException(
                "The type of this container can't be changed."
            );
        }

        $viewBuilder = $this->editor->getViewBuilder();

        $removed = [];

        $fromPlugin = $this->editor->getContainerPlugin($container->getType());
        $toPlugin = $this->editor->getContainerPlugin($type);

        // If we switch from Copy plugin and a copied container is set
        if ($fromPlugin instanceof CopyPlugin && null !== $copy = $container->getCopy()) {
            // Fake copied inner container
            $removed[] = $viewBuilder->buildContainer($copy)->getInnerAttributes()->getId();
        }

        // Plugin removal
        $fromPlugin->remove($container);

        // Sets the new type
        $container->setType($type);

        // If we are switching to Copy plugin
        if ($toPlugin instanceof CopyPlugin) {
            // Remove previous rows (as we'll display the copied container's ones)
            foreach ($container->getRows() as $row) {
                $removed[] = $viewBuilder->buildRow($row)->getAttributes()->getId();
                $this->editor->getRowManager()->delete($row, true);
            }
        }
        // Create default row and block if switching to any but 'any' plugin
        elseif (0 == $container->getRows()->count()) {
            $this->editor->getRowManager()->create($container);
        }

        // Plugin creation
        $toPlugin->create($container, $data);

        return $removed;
    }

    /**
     * Deletes the container.
     *
     * @param Model\ContainerInterface $container
     *
     * @return Model\ContainerInterface The removed container.
     * @throws InvalidOperationException
     */
    public function delete(Model\ContainerInterface $container): Model\ContainerInterface
    {
        // Ensure not named / alone
        if ($container->isAlone() || $container->isNamed() || $container->isTitled()) {
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
    public function moveUp(Model\ContainerInterface $container): Model\ContainerInterface
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
    public function moveDown(Model\ContainerInterface $container): Model\ContainerInterface
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
    public function fixRowsPositions(Model\ContainerInterface $container): ContainerManager
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
