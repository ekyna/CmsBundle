<?php

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
    public function createContent();

    /**
     * Creates a new container.
     *
     * @return EM\ContainerInterface
     */
    public function createContainer();

    /**
     * Creates a new row.
     *
     * @return EM\RowInterface
     */
    public function createRow();

    /**
     * Creates a new block.
     *
     * @return EM\BlockInterface
     */
    public function createBlock();

    /**
     * Finds the content by id.
     *
     * @param int $id
     *
     * @return EM\ContentInterface
     */
    public function findContentById($id);

    /**
     * Finds the container by id.
     *
     * @param int $id
     *
     * @return EM\ContainerInterface|null
     */
    public function findContainerById($id);

    /**
     * Finds the row by id.
     *
     * @param int $id
     *
     * @return EM\RowInterface|null
     */
    public function findRowById($id);

    /**
     * Finds the block by id.
     *
     * @param int $id
     *
     * @return EM\BlockInterface|null
     */
    public function findBlockById($id);

    /**
     * Finds the content by name.
     *
     * @param int $name
     *
     * @return EM\ContentInterface
     */
    public function findContentByName($name);

    /**
     * Finds the container by name.
     *
     * @param int $name
     *
     * @return EM\ContainerInterface|null
     */
    public function findContainerByName($name);

    /**
     * Finds the row by name.
     *
     * @param int $name
     *
     * @return EM\RowInterface|null
     */
    public function findRowByName($name);

    /**
     * Finds the block by name.
     *
     * @param int $name
     *
     * @return EM\BlockInterface|null
     */
    public function findBlockByName($name);

    /**
     * Returns the sibling of the given container.
     *
     * @param EM\ContainerInterface $container
     * @param bool            $next Whether to look for the next or the previous
     *
     * @return EM\ContainerInterface|null
     */
    public function findSiblingContainer(EM\ContainerInterface $container, $next = false);

    /**
     * Returns the sibling of the given row.
     *
     * @param EM\RowInterface $row
     * @param bool            $next Whether to look for the next or the previous
     *
     * @return EM\RowInterface|null
     */
    public function findSiblingRow(EM\RowInterface $row, $next = false);

    /**
     * Returns the sibling of the given block.
     *
     * @param EM\BlockInterface $block
     * @param bool            $next Whether to look for the next or the previous
     *
     * @return EM\BlockInterface|null
     */
    public function findSiblingBlock(EM\BlockInterface $block, $next = false);

    /**
     * Loads and returns the subject's content.
     *
     * @param CM\ContentSubjectInterface $subject
     *
     * @return EM\ContentInterface|null
     */
    public function loadSubjectContent(CM\ContentSubjectInterface $subject);
}
