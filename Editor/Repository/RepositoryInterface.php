<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Repository;

use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Bundle\CmsBundle\Model as CM;

/**
 * Interface RepositoryInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface RepositoryInterface
{
    /**
     * Creates a new content.
     *
     * @return EM\ContentInterface
     */
    public function createContent(): EM\ContentInterface;

    /**
     * Creates a new container.
     *
     * @return EM\ContainerInterface
     */
    public function createContainer(): EM\ContainerInterface;

    /**
     * Creates a new row.
     *
     * @return EM\RowInterface
     */
    public function createRow(): EM\RowInterface;

    /**
     * Creates a new block.
     *
     * @return EM\BlockInterface
     */
    public function createBlock(): EM\BlockInterface;

    /**
     * Finds the content by id.
     *
     * @param int $id
     *
     * @return EM\ContentInterface|null
     */
    public function findContentById(int $id): ?EM\ContentInterface;

    /**
     * Finds the container by id.
     *
     * @param int $id
     *
     * @return EM\ContainerInterface|null
     */
    public function findContainerById(int $id): ?EM\ContainerInterface;

    /**
     * Finds the row by id.
     *
     * @param int $id
     *
     * @return EM\RowInterface|null
     */
    public function findRowById(int $id): ?EM\RowInterface;

    /**
     * Finds the block by id.
     *
     * @param int $id
     *
     * @return EM\BlockInterface|null
     */
    public function findBlockById(int $id): ?EM\BlockInterface;

    /**
     * Finds the content by name.
     *
     * @param string $name
     *
     * @return EM\ContentInterface|null
     */
    public function findContentByName(string $name): ?EM\ContentInterface;

    /**
     * Finds the container by name.
     *
     * @param string $name
     *
     * @return EM\ContainerInterface|null
     */
    public function findContainerByName(string $name): ?EM\ContainerInterface;

    /**
     * Returns the container copies.
     *
     * @param EM\ContainerInterface $container
     *
     * @return array<int, EM\ContainerInterface>
     */
    public function findContainerCopies(EM\ContainerInterface $container): array;

    /**
     * Finds the row by name.
     *
     * @param string $name
     *
     * @return EM\RowInterface|null
     */
    public function findRowByName(string $name): ?EM\RowInterface;

    /**
     * Finds the block by name.
     *
     * @param string $name
     *
     * @return EM\BlockInterface|null
     */
    public function findBlockByName(string $name): ?EM\BlockInterface;

    /**
     * Returns the sibling of the given container.
     *
     * @param EM\ContainerInterface $container
     * @param bool                  $next Whether to look for the next or the previous
     *
     * @return EM\ContainerInterface|null
     */
    public function findSiblingContainer(EM\ContainerInterface $container, bool $next = false): ?EM\ContainerInterface;

    /**
     * Returns the sibling of the given row.
     *
     * @param EM\RowInterface $row
     * @param bool            $next Whether to look for the next or the previous
     *
     * @return EM\RowInterface|null
     */
    public function findSiblingRow(EM\RowInterface $row, bool $next = false): ?EM\RowInterface;

    /**
     * Returns the sibling of the given block.
     *
     * @param EM\BlockInterface $block
     * @param bool              $next Whether to look for the next or the previous
     *
     * @return EM\BlockInterface|null
     */
    public function findSiblingBlock(EM\BlockInterface $block, bool $next = false): ?EM\BlockInterface;

    /**
     * Loads and returns the subject's content.
     *
     * @param CM\ContentSubjectInterface $subject
     *
     * @return EM\ContentInterface|null
     */
    public function loadSubjectContent(CM\ContentSubjectInterface $subject): ?EM\ContentInterface;
}
