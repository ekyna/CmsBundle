<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Repository;

use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Editor\Model;
use Ekyna\Bundle\CmsBundle\Entity\Editor as entity;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Repository\BlockRepository;
use Ekyna\Bundle\CmsBundle\Repository\ContainerRepository;
use Ekyna\Bundle\CmsBundle\Repository\ContentRepository;
use Ekyna\Bundle\CmsBundle\Repository\RowRepository;
use Ekyna\Component\Resource\Factory\FactoryFactoryInterface;
use Ekyna\Component\Resource\Factory\ResourceFactoryInterface;
use Ekyna\Component\Resource\Model\SortableInterface;
use Ekyna\Component\Resource\Repository\RepositoryFactoryInterface;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Class Repository
 * @package Ekyna\Bundle\CmsBundle\Editor\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Repository implements RepositoryInterface
{
    private FactoryFactoryInterface    $factoryFactory;
    private RepositoryFactoryInterface $repositoryFactory;
    private array                      $classes;


    /**
     * Constructor.
     *
     * @param FactoryFactoryInterface    $factoryFactory
     * @param RepositoryFactoryInterface $repositoryFactory
     * @param array                      $classes
     */
    public function __construct(
        FactoryFactoryInterface $factoryFactory,
        RepositoryFactoryInterface $repositoryFactory,
        array $classes
    ) {
        $this->factoryFactory = $factoryFactory;
        $this->repositoryFactory = $repositoryFactory;
        $this->classes = array_replace([
            Model\ContentInterface::class   => Entity\Content::class,
            Model\ContainerInterface::class => Entity\Container::class,
            Model\RowInterface::class       => Entity\Row::class,
            Model\BlockInterface::class     => Entity\Block::class,
        ], $classes);
    }

    /**
     * @inheritDoc
     */
    public function createContent(): Model\ContentInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getFactory(Model\ContentInterface::class)->create();
    }

    /**
     * @inheritDoc
     */
    public function createContainer(): Model\ContainerInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getFactory(Model\ContainerInterface::class)->create();
    }

    /**
     * @inheritDoc
     */
    public function createRow(): Model\RowInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getFactory(Model\RowInterface::class)->create();
    }

    /**
     * @inheritDoc
     */
    public function createBlock(): Model\BlockInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getFactory(Model\BlockInterface::class)->create();
    }

    /**
     * @inheritDoc
     */
    public function findContentById(int $id): ?Model\ContentInterface
    {
        return $this->getContentRepository()->findOneById($id);
    }

    /**
     * @inheritDoc
     */
    public function findContainerById(int $id): ?Model\ContainerInterface
    {
        return $this->getContainerRepository()->findOneById($id);
    }

    /**
     * @inheritDoc
     */
    public function findRowById(int $id): ?Model\RowInterface
    {
        return $this->getRowRepository()->findOneById($id);
    }

    /**
     * @inheritDoc
     */
    public function findBlockById(int $id): ?Model\BlockInterface
    {
        return $this->getBlockRepository()->findOneById($id);
    }

    /**
     * @inheritDoc
     */
    public function findContentByName(string $name): ?Model\ContentInterface
    {
        return $this->getContentRepository()->findOneByName($name);
    }

    /**
     * @inheritDoc
     */
    public function findContainerByName(string $name): ?Model\ContainerInterface
    {
        return $this->getContainerRepository()->findOneByName($name);
    }

    /**
     * @inheritDoc
     */
    public function findRowByName(string $name): ?Model\RowInterface
    {
        return $this->getRowRepository()->findOneByName($name);
    }

    /**
     * @inheritDoc
     */
    public function findBlockByName(string $name): ?Model\BlockInterface
    {
        return $this->getBlockRepository()->findOneByName($name);
    }

    /**
     * Returns the sibling of the given container.
     *
     * @param Model\ContainerInterface $container
     * @param bool                     $next Whether to look for the next or the previous
     *
     * @return Model\ContainerInterface|null
     */
    public function findSiblingContainer(
        Model\ContainerInterface $container,
        bool $next = false
    ): ?Model\ContainerInterface {
        if (null === $content = $container->getContent()) {
            return null;
        }

        return $this->findSibling($content->getContainers(), $container, $next);
    }

    /**
     * Returns the sibling of the given row.
     *
     * @param Model\RowInterface $row
     * @param bool               $next Whether to look for the next or the previous
     *
     * @return Model\RowInterface|null
     */
    public function findSiblingRow(Model\RowInterface $row, bool $next = false): ?Model\RowInterface
    {
        if (null === $container = $row->getContainer()) {
            return null;
        }

        return $this->findSibling($container->getRows(), $row, $next);
    }

    /**
     * Returns the sibling of the given block.
     *
     * @param Model\BlockInterface $block
     * @param bool                 $next Whether to look for the next or the previous
     *
     * @return Model\BlockInterface|null
     */
    public function findSiblingBlock(Model\BlockInterface $block, bool $next = false): ?Model\BlockInterface
    {
        if (null === $row = $block->getRow()) {
            return null;
        }

        return $this->findSibling($row->getBlocks(), $block, $next);
    }

    /**
     * Finds the previous element based on position.
     *
     * @param Collection        $elements
     * @param SortableInterface $current
     * @param bool              $next Whether to look for the next or the previous
     *
     * @return mixed
     */
    private function findSibling(Collection $elements, SortableInterface $current, bool $next = false)
    {
        if ($next) {
            $sibling = $elements->filter(function (SortableInterface $s) use ($current) {
                return $s->getPosition() > $current->getPosition();
            })->first();
        } else {
            $sibling = $elements->filter(function (SortableInterface $s) use ($current) {
                return $s->getPosition() < $current->getPosition();
            })->last();
        }

        return $sibling ?: null;
    }

    /**
     * @inheritDoc
     */
    public function loadSubjectContent(ContentSubjectInterface $subject): ?Model\ContentInterface
    {
        return $this->getContentRepository()->findBySubject($subject);
    }

    private function getContentRepository(): ContentRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getRepository(Model\ContentInterface::class);
    }

    private function getContainerRepository(): ContainerRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getRepository(Model\ContainerInterface::class);
    }

    private function getRowRepository(): RowRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getRepository(Model\RowInterface::class);
    }

    private function getBlockRepository(): BlockRepository
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getRepository(Model\BlockInterface::class);
    }

    private function getFactory(string $interface): ResourceFactoryInterface
    {
        return $this->factoryFactory->getFactory(
            $this->classes[$interface]
        );
    }

    private function getRepository(string $interface): ResourceRepositoryInterface
    {
        return $this->repositoryFactory->getRepository(
            $this->classes[$interface]
        );
    }
}
