<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Repository;

use Ekyna\Bundle\CmsBundle\Model;

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
     * @return Model\ContentInterface
     */
    public function createContent();

    /**
     * Creates a new container.
     *
     * @return Model\ContainerInterface
     */
    public function createContainer();

    /**
     * Creates a new row.
     *
     * @return Model\RowInterface
     */
    public function createRow();

    /**
     * Creates a new block.
     *
     * @return Model\BlockInterface
     */
    public function createBlock();

    /**
     * Finds the content by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface
     */
    public function findContentById($id);

    /**
     * Finds the container by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContainerInterface|null
     */
    public function findContainerById($id);

    /**
     * Finds the row by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\RowInterface|null
     */
    public function findRowById($id);

    /**
     * Finds the block by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\BlockInterface|null
     */
    public function findBlockById($id);

    /**
     * Finds the content by name.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface
     */
    public function findContentByName($name);

    /**
     * Finds the container by name.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContainerInterface|null
     */
    public function findContainerByName($name);

    /**
     * Finds the row by name.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\RowInterface|null
     */
    public function findRowByName($name);

    /**
     * Finds the block by name.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\BlockInterface|null
     */
    public function findBlockByName($name);

    /**
     * Loads and returns the subject's content.
     *
     * @param Model\ContentSubjectInterface $subject
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface|null
     */
    public function loadSubjectContent(Model\ContentSubjectInterface $subject);
}
